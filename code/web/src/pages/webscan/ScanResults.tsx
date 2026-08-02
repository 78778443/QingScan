import { useMemo, useState } from 'react'
import { useParams } from 'react-router-dom'
import { useQuery } from '@tanstack/react-query'
import { ArrowsClockwiseIcon, EyeIcon, InfoIcon, MagnifyingGlassIcon } from '@phosphor-icons/react'

import { apiPage } from '@/lib/api'
import { DataTable, type Column } from '@/components/DataTable'
import { Badge } from '@/components/ui/badge'
import { Button } from '@/components/ui/button'
import {
  Dialog,
  DialogContent,
  DialogHeader,
  DialogTitle,
} from '@/components/ui/dialog'
import { Input } from '@/components/ui/input'
import {
  Select,
  SelectContent,
  SelectItem,
  SelectTrigger,
  SelectValue,
} from '@/components/ui/select'
import { cn } from '@/lib/utils'

// ---------- 类型与配置 ----------

type ToolKey = 'xray' | 'nuclei' | 'sqlmap' | 'vulmap' | 'dirmap' | 'whatweb'

type ScanRow = Record<string, unknown>

interface ToolConfig {
  key: ToolKey
  title: string
  api: string
  showLevel: boolean
  // 级别筛选值：展示名 → 接口值（nuclei 后端按 severity 精确匹配，需要小写）
  levelApiValues: Record<string, string>
  showCheckStatus: boolean
  // 仅包含工具专属列；ID/目标/URL/时间/操作 由 buildColumns 统一拼装
  columns: Column<ScanRow>[]
}

// ---------- 容错取值工具函数 ----------

function text(value: unknown): string {
  if (value === null || value === undefined) return ''
  if (typeof value === 'object') {
    try {
      return JSON.stringify(value)
    } catch {
      return String(value)
    }
  }
  return String(value)
}

function truncate(value: string, max = 80): string {
  const flat = value.replace(/\s+/g, ' ').trim()
  return flat.length > max ? `${flat.slice(0, max)}…` : flat
}

function pretty(value: unknown): string {
  if (value === null || value === undefined) return '-'
  if (typeof value === 'object') {
    try {
      return JSON.stringify(value, null, 2)
    } catch {
      return String(value)
    }
  }
  return String(value)
}

// ---------- 严重级别（shared 函数） ----------

const SEVERITY_STYLES: Record<string, string> = {
  Critical: 'bg-red-500/10 text-red-600',
  High: 'bg-orange-500/10 text-orange-600',
  Medium: 'bg-yellow-500/10 text-yellow-600',
  Low: 'bg-green-500/10 text-green-600',
}

function normalizeSeverity(value: unknown): string {
  if (value === null || value === undefined || value === '') return ''
  const s = String(value)
  if (/^\d+$/.test(s)) {
    const idx = Number(s)
    if (idx >= 0 && idx <= 3) return ['Low', 'Medium', 'High', 'Critical'][idx]
    return s
  }
  const lower = s.toLowerCase()
  for (const sev of ['Critical', 'High', 'Medium', 'Low']) {
    if (sev.toLowerCase() === lower) return sev
  }
  return s
}

function SeverityBadge({ value }: { value: unknown }) {
  const sev = normalizeSeverity(value)
  if (!sev) return <Badge variant="outline">未知</Badge>
  return <Badge className={SEVERITY_STYLES[sev] ?? 'bg-muted text-muted-foreground'}>{sev}</Badge>
}

// xray 审核状态：0 未审核 / 1 有效漏洞 / 2 无效漏洞
const CHECK_STATUS_META: Record<string, { label: string; className: string }> = {
  '0': { label: '未审核', className: 'bg-muted text-muted-foreground' },
  '1': { label: '有效漏洞', className: 'bg-green-500/10 text-green-600' },
  '2': { label: '无效漏洞', className: 'bg-orange-500/10 text-orange-600' },
}

function CheckStatusBadge({ value }: { value: unknown }) {
  const meta = CHECK_STATUS_META[String(value ?? '')] ?? {
    label: text(value) || '未知',
    className: 'bg-muted text-muted-foreground',
  }
  return <Badge className={meta.className}>{meta.label}</Badge>
}

function CodeBadge({ value }: { value: unknown }) {
  const s = text(value)
  if (!s) return <span className="text-muted-foreground">-</span>
  const n = Number(s)
  let cls = 'bg-muted text-muted-foreground'
  if (Number.isFinite(n)) {
    if (n >= 400) cls = 'bg-red-500/10 text-red-600'
    else if (n >= 300) cls = 'bg-yellow-500/10 text-yellow-600'
    else if (n >= 200) cls = 'bg-green-500/10 text-green-600'
  }
  return <Badge className={cls}>{s}</Badge>
}

// xray 的 plugin 为 JSON 字符串，容错解析后取 name
function xrayPluginName(value: unknown): string {
  if (typeof value !== 'string') {
    const s = text(value)
    return s ? truncate(s, 60) : '-'
  }
  const trimmed = value.trim()
  if (!trimmed) return '-'
  if (trimmed.startsWith('{') || trimmed.startsWith('[')) {
    try {
      const parsed = JSON.parse(trimmed) as unknown
      if (Array.isArray(parsed)) {
        return truncate(text(parsed[0]) || trimmed, 60)
      }
      if (parsed && typeof parsed === 'object') {
        const p = parsed as Record<string, unknown>
        const name = p.name ?? p.plugin ?? p.cve ?? p.category
        if (name) return String(name)
        return truncate(JSON.stringify(p), 60)
      }
    } catch {
      // JSON 解析失败时按原样展示
    }
  }
  return truncate(trimmed, 60)
}

// ---------- 通用单元格 ----------

function AppNameCell({ value }: { value: unknown }) {
  const s = text(value)
  if (!s) return <span className="text-muted-foreground">-</span>
  return (
    <Badge variant="secondary" className="max-w-40 truncate" title={s}>
      {s}
    </Badge>
  )
}

function UrlCell({ value }: { value: unknown }) {
  const s = text(value)
  if (!s) return <span className="text-muted-foreground">-</span>
  return (
    <span className="inline-block max-w-64 truncate align-middle font-mono" title={s}>
      {s}
    </span>
  )
}

function DetailButton({ onClick }: { onClick: () => void }) {
  return (
    <Button variant="ghost" size="xs" onClick={onClick}>
      <EyeIcon />
      详情
    </Button>
  )
}

// ---------- 各工具列配置 ----------

function buildColumns(cfg: ToolConfig, openDetail: (row: ScanRow) => void): Column<ScanRow>[] {
  return [
    {
      key: 'id',
      header: 'ID',
      className: 'w-14 text-muted-foreground',
      render: (r) => String(r.id ?? '-'),
    },
    {
      key: 'app_name',
      header: '目标',
      render: (r) => <AppNameCell value={r.app_name} />,
    },
    {
      key: 'url',
      header: 'URL',
      render: (r) => <UrlCell value={r.url ?? r.host ?? r.target} />,
    },
    ...cfg.columns,
    {
      key: 'create_time',
      header: '时间',
      className: 'whitespace-nowrap',
      render: (r) => text(r.create_time) || '-',
    },
    {
      key: 'actions',
      header: '操作',
      className: 'w-20',
      render: (r) => <DetailButton onClick={() => openDetail(r)} />,
    },
  ]
}

const TOOL_CONFIGS: Record<ToolKey, ToolConfig> = {
  xray: {
    key: 'xray',
    title: 'XRay 漏洞',
    api: '/api/webscan/xray_list',
    showLevel: true,
    levelApiValues: { Low: 'Low', Medium: 'Medium', High: 'High', Critical: 'Critical' },
    showCheckStatus: true,
    columns: [
      {
        key: 'plugin',
        header: '漏洞名称',
        render: (r) => (
          <span className="inline-block max-w-52 truncate align-middle" title={text(r.plugin)}>
            {xrayPluginName(r.plugin)}
          </span>
        ),
      },
      {
        key: 'hazard_level',
        header: '严重级别',
        render: (r) => <SeverityBadge value={r.hazard_level} />,
      },
      {
        key: 'payload',
        header: 'Payload',
        render: (r) => (
          <span className="inline-block max-w-56 truncate align-middle font-mono text-muted-foreground" title={text(r.payload)}>
            {truncate(text(r.payload), 60) || '-'}
          </span>
        ),
      },
      {
        key: 'check_status',
        header: '审核状态',
        render: (r) => <CheckStatusBadge value={r.check_status} />,
      },
    ],
  },
  nuclei: {
    key: 'nuclei',
    title: 'Nuclei 漏洞',
    api: '/api/webscan/nuclei_list',
    showLevel: true,
    // 后端按 severity 精确匹配（存储为小写）
    levelApiValues: { Low: 'low', Medium: 'medium', High: 'high', Critical: 'critical' },
    showCheckStatus: false,
    columns: [
      {
        key: 'name',
        header: '漏洞名称',
        render: (r) => (
          <span className="inline-block max-w-52 truncate align-middle" title={text(r.vuln_name ?? r.name)}>
            {truncate(text(r.vuln_name ?? r.name), 50) || '-'}
          </span>
        ),
      },
      {
        key: 'severity',
        header: '严重级别',
        render: (r) => <SeverityBadge value={r.severity ?? r.level} />,
      },
      {
        key: 'info',
        header: '详情',
        render: (r) => (
          <span className="inline-block max-w-56 truncate align-middle text-muted-foreground" title={text(r.info)}>
            {truncate(text(r.info), 60) || '-'}
          </span>
        ),
      },
    ],
  },
  sqlmap: {
    key: 'sqlmap',
    title: 'Sqlmap 注入',
    api: '/api/webscan/sqlmap_list',
    showLevel: false,
    levelApiValues: {},
    showCheckStatus: false,
    columns: [
      {
        key: 'title',
        header: '标题',
        render: (r) => (
          <span className="inline-block max-w-44 truncate align-middle" title={text(r.title)}>
            {truncate(text(r.title), 40) || '-'}
          </span>
        ),
      },
      {
        key: 'result',
        header: '结果',
        render: (r) => (
          <span className="inline-block max-w-64 truncate align-middle font-mono text-muted-foreground" title={text(r.result)}>
            {truncate(text(r.result), 80) || '-'}
          </span>
        ),
      },
      {
        key: 'payload',
        header: 'Payload',
        render: (r) => (
          <span className="inline-block max-w-48 truncate align-middle font-mono text-muted-foreground" title={text(r.payload)}>
            {truncate(text(r.payload), 40) || '-'}
          </span>
        ),
      },
    ],
  },
  vulmap: {
    key: 'vulmap',
    title: 'Vulmap 漏洞',
    api: '/api/webscan/vulmap_list',
    showLevel: true,
    levelApiValues: { Low: 'low', Medium: 'medium', High: 'high', Critical: 'critical' },
    showCheckStatus: false,
    columns: [
      {
        key: 'name',
        header: '漏洞名称',
        render: (r) => (
          <span className="inline-block max-w-52 truncate align-middle" title={text(r.vuln_name ?? r.name)}>
            {truncate(text(r.vuln_name ?? r.name), 50) || '-'}
          </span>
        ),
      },
      {
        key: 'level',
        header: '严重级别',
        render: (r) => <SeverityBadge value={r.level ?? r.severity} />,
      },
      {
        key: 'description',
        header: '描述',
        render: (r) => (
          <span className="inline-block max-w-64 truncate align-middle text-muted-foreground" title={text(r.description ?? r.info)}>
            {truncate(text(r.description ?? r.info), 80) || '-'}
          </span>
        ),
      },
    ],
  },
  dirmap: {
    key: 'dirmap',
    title: 'Dirmap 目录',
    api: '/api/webscan/dirmap_list',
    showLevel: false,
    levelApiValues: {},
    showCheckStatus: false,
    columns: [
      {
        key: 'status_code',
        header: '状态码',
        render: (r) => <CodeBadge value={r.status_code ?? r.statuscode} />,
      },
      {
        key: 'type',
        header: '类型',
        render: (r) => <span className="text-muted-foreground">{text(r.type) || '-'}</span>,
      },
      {
        key: 'title',
        header: '标题 / 长度',
        render: (r) => (
          <span className="inline-block max-w-48 truncate align-middle text-muted-foreground" title={text(r.title ?? r.len)}>
            {truncate(text(r.title ?? r.len), 40) || '-'}
          </span>
        ),
      },
    ],
  },
  whatweb: {
    key: 'whatweb',
    title: 'WhatWeb 指纹',
    api: '/api/webscan/whatweb_list',
    showLevel: false,
    levelApiValues: {},
    showCheckStatus: false,
    columns: [
      {
        key: 'whatweb',
        header: '指纹结果',
        render: (r) => (
          <span className="inline-block max-w-96 truncate align-middle text-muted-foreground" title={text(r.whatweb ?? r.result)}>
            {truncate(text(r.whatweb ?? r.result), 100) || '-'}
          </span>
        ),
      },
    ],
  },
}

const LEVEL_OPTIONS = [
  { label: '全部级别', value: 'all' },
  { label: 'Critical', value: 'Critical' },
  { label: 'High', value: 'High' },
  { label: 'Medium', value: 'Medium' },
  { label: 'Low', value: 'Low' },
]

// ---------- 页面组件 ----------

export default function ScanResults() {
  const { tool } = useParams()

  const toolKey = useMemo<ToolKey | null>(() => {
    if (tool && tool in TOOL_CONFIGS) return tool as ToolKey
    return null
  }, [tool])

  const config = toolKey ? TOOL_CONFIGS[toolKey] : null

  // 筛选状态（点击“查询”后提交）
  const [page, setPage] = useState(1)
  const [search, setSearch] = useState('')
  const [level, setLevel] = useState('all')
  const [checkStatus, setCheckStatus] = useState('all')
  const [filters, setFilters] = useState({ search: '', level: 'all', checkStatus: 'all' })
  const [detailRow, setDetailRow] = useState<ScanRow | null>(null)

  const { data, isLoading, isFetching, refetch } = useQuery({
    queryKey: ['scan_results', tool, page, filters],
    enabled: config !== null,
    queryFn: () => {
      if (!config) throw new Error('未知的扫描工具')
      const apiLevel =
        filters.level === 'all' ? undefined : (config.levelApiValues[filters.level] ?? filters.level)
      return apiPage<ScanRow>(config.api, {
        page,
        limit: 20,
        search: filters.search || undefined,
        level: config.showLevel && apiLevel ? apiLevel : undefined,
        check_status: config.showCheckStatus && filters.checkStatus !== 'all' ? filters.checkStatus : undefined,
      })
    },
  })

  const columns = useMemo<Column<ScanRow>[]>(() => {
    if (!config) return []
    return buildColumns(config, (row) => setDetailRow(row))
  }, [config])

  const handleSearch = () => {
    setPage(1)
    setFilters({ search: search.trim(), level, checkStatus })
  }

  if (!config) {
    return (
      <div className="flex flex-col items-center justify-center gap-3 py-24 text-muted-foreground">
        <InfoIcon className="size-10" />
        <p className="text-sm">未知的扫描工具：{tool ?? '未指定'}</p>
        <Button variant="outline" size="sm" onClick={() => window.history.back()}>
          返回上一页
        </Button>
      </div>
    )
  }

  const toolbar = (
    <div className="flex flex-wrap items-center gap-2">
      <div className="relative">
        <MagnifyingGlassIcon className="absolute top-1/2 left-2 size-4 -translate-y-1/2 text-muted-foreground" />
        <Input
          className="h-8 w-64 pl-7"
          placeholder="搜索 URL / 漏洞名"
          value={search}
          onChange={(e) => setSearch(e.target.value)}
          onKeyDown={(e) => {
            if (e.key === 'Enter') handleSearch()
          }}
        />
      </div>
      {config.showLevel && (
        <Select value={level} onValueChange={setLevel}>
          <SelectTrigger className="h-8 w-32">
            <SelectValue placeholder="严重级别" />
          </SelectTrigger>
          <SelectContent>
            {LEVEL_OPTIONS.map((o) => (
              <SelectItem key={o.value} value={o.value}>
                {o.label}
              </SelectItem>
            ))}
          </SelectContent>
        </Select>
      )}
      {config.showCheckStatus && (
        <Select value={checkStatus} onValueChange={setCheckStatus}>
          <SelectTrigger className="h-8 w-32">
            <SelectValue placeholder="审核状态" />
          </SelectTrigger>
          <SelectContent>
            <SelectItem value="all">全部状态</SelectItem>
            <SelectItem value="0">未审核</SelectItem>
            <SelectItem value="1">已审核</SelectItem>
          </SelectContent>
        </Select>
      )}
      <Button variant="outline" className="h-8" onClick={handleSearch}>
        查询
      </Button>
      <Button variant="outline" className="h-8" onClick={() => refetch()} disabled={isFetching} title="刷新">
        <ArrowsClockwiseIcon className={cn('size-4', isFetching && 'animate-spin')} />
      </Button>
    </div>
  )

  return (
    <div className="space-y-4">
      <DataTable<ScanRow>
        columns={columns}
        rows={data?.rows ?? []}
        loading={isLoading}
        total={data?.count}
        currentPage={data?.current_page}
        totalPage={data?.total_page}
        onPageChange={setPage}
        rowKey={(r) => Number(r.id) || 0}
        toolbar={toolbar}
        emptyText="暂无扫描结果"
      />

      {/* 详情 */}
      <Dialog open={detailRow !== null} onOpenChange={(v) => { if (!v) setDetailRow(null) }}>
        <DialogContent className="max-h-[80vh] overflow-y-auto sm:max-w-xl">
          <DialogHeader>
            <DialogTitle>{config.title} · 结果详情</DialogTitle>
          </DialogHeader>
          <div className="divide-y">
            {detailRow &&
              Object.entries(detailRow).map(([k, v]) => (
                <div key={k} className="flex gap-3 py-2">
                  <span className="w-36 shrink-0 break-all text-muted-foreground">{k}</span>
                  <span className="min-w-0 break-all font-mono text-xs whitespace-pre-wrap">{pretty(v)}</span>
                </div>
              ))}
          </div>
        </DialogContent>
      </Dialog>
    </div>
  )
}
