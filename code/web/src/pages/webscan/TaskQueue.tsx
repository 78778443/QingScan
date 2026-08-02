import { useState } from 'react'
import { useQuery } from '@tanstack/react-query'
import { apiPage } from '@/lib/api'
import { DataTable, severityColor, statusBadge, type Column } from '@/components/DataTable'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { Badge } from '@/components/ui/badge'
import {
  Select,
  SelectContent,
  SelectItem,
  SelectTrigger,
  SelectValue,
} from '@/components/ui/select'
import { MagnifyingGlass, ArrowsClockwise } from '@phosphor-icons/react'

type Row = Record<string, unknown>

function str(v: unknown): string {
  if (v === undefined || v === null || v === '') return '-'
  if (typeof v === 'object') return JSON.stringify(v)
  return String(v)
}

// 任务状态：0 待执行 / 1 执行中 / 2 已完成，其他值容错显示原值
function taskStatus(v: unknown): { text: string; matched: boolean } {
  const raw = String(v ?? '').trim()
  switch (raw) {
    case '0':
      return { text: '待处理', matched: true }
    case '1':
      return { text: '执行中', matched: true }
    case '2':
      return { text: '已完成', matched: true }
    default:
      return { text: raw === '' ? '-' : raw, matched: false }
  }
}

function TaskStatusBadge({ value }: { value: unknown }) {
  const { text, matched } = taskStatus(value)
  if (!matched) {
    return <Badge variant="outline">{text}</Badge>
  }
  if (text === '待处理') return statusBadge('0')
  if (text === '已完成') return <Badge className={severityColor('low')}>{text}</Badge>
  return <Badge className="bg-blue-500/15 text-blue-600 dark:text-blue-400">{text}</Badge>
}

const STATUS_OPTIONS = [
  { value: '', label: '全部' },
  { value: '0', label: '待处理' },
  { value: '1', label: '执行中' },
  { value: '2', label: '已完成' },
]

// 任务内部标识（task_scan.tool）→ 功能名
const TOOL_LABELS: Record<string, string> = {
  scan_app_web_vuln: 'Web漏洞检测',
  scan_app_gen_vuln: '通用漏洞扫描',
  scan_app_vul_verify: '漏洞验证',
  scan_app_dir_scan: '目录扫描',
  scan_app_finger: '指纹识别',
  scan_app_dismap: '资产指纹',
  scan_app_crawler: '爬虫抓取',
  scan_app_crawlergo: '爬虫抓取',
  scan_app_awvs: 'AWVS 扫描',
  scan_url_sql_inject: 'SQL注入检测',
  scan_ip_weak_pass: '弱口令爆破',
  scan_app_jietu: '网站截图',
  scan_app_google: '基础信息',
  asm_domain_subdomain: '子域名枚举',
  asm_ip_port_scan: '端口扫描',
  asm_ip_info: 'IP定位',
  asm_domain_fofa: 'FOFA资产',
  asm_discover_fofa: 'FOFA资产',
  code_fortify: '代码审计',
  code_semgrep: '规则扫描',
  code_murphysec: '依赖检查',
  code_codeql: '代码审计',
}

function toolLabel(v: unknown): string {
  const raw = String(v ?? '')
  return TOOL_LABELS[raw] ?? raw.replace(/^scan_(app|url|ip)_|^asm_|^code_/, '')
}

const columns: Column<Row>[] = [
  { key: 'id', header: 'ID', className: 'w-16' },
  {
    key: 'tool',
    header: '任务类型',
    render: (row) => (
      <Badge className="bg-blue-500/15 text-blue-600 dark:text-blue-400">{toolLabel(row.tool)}</Badge>
    ),
  },
  { key: 'target_table', header: '目标类型' },
  {
    key: 'target',
    header: '目标',
    className: 'max-w-56 truncate',
    render: (row) => {
      const ext = str(row.ext_info)
      let target = str(row.target)
      try {
        const parsed = JSON.parse(ext)
        if (parsed.url) target = String(parsed.url)
        else if (parsed.ip) target = String(parsed.ip)
        else if (parsed.host) target = String(parsed.host)
      } catch { /* 忽略 */ }
      return (
        <span className="block max-w-56 truncate" title={target}>
          {target}
        </span>
      )
    },
  },
  {
    key: 'ext_info',
    header: '扩展信息',
    className: 'max-w-72 truncate',
    render: (row) => {
      const text = str(row.ext_info)
      return (
        <span className="block max-w-72 truncate font-mono" title={text}>
          {text}
        </span>
      )
    },
  },
  {
    key: 'status',
    header: '状态',
    render: (row) => <TaskStatusBadge value={row.status} />,
  },
  {
    key: 'create_time',
    header: '创建时间',
    className: 'whitespace-nowrap',
  },
  {
    key: 'update_time',
    header: '更新时间',
    className: 'whitespace-nowrap',
  },
]

export default function TaskQueue() {
  const [page, setPage] = useState(1)
  const [tool, setTool] = useState('')
  const [status, setStatus] = useState('')
  const [toolDraft, setToolDraft] = useState('')

  const applyFilter = () => {
    setTool(toolDraft.trim())
    setPage(1)
  }

  const { data, isLoading, refetch } = useQuery({
    queryKey: ['tasks', page, tool, status],
    queryFn: () =>
      apiPage<Row>('/task/list', {
        page,
        limit: 10,
        tool,
        status,
      }),
  })

  const refresh = () => {
    if (page === 1) {
      refetch()
    } else {
      setPage(1)
    }
  }

  const toolbar = (
    <div className="flex flex-wrap items-center gap-2 rounded-lg border bg-card p-3">
      <Input
        placeholder="任务类型"
        value={toolDraft}
        onChange={(e) => setToolDraft(e.target.value)}
        onKeyDown={(e) => {
          if (e.key === 'Enter') applyFilter()
        }}
        className="w-56"
      />
      <Select
        value={status}
        onValueChange={(value) => {
          setStatus(value)
          setPage(1)
        }}
      >
        <SelectTrigger className="w-28">
          <SelectValue placeholder="任务状态" />
        </SelectTrigger>
        <SelectContent>
          {STATUS_OPTIONS.map((opt) => (
            <SelectItem key={opt.value} value={opt.value}>
              {opt.label}
            </SelectItem>
          ))}
        </SelectContent>
      </Select>
      <Button onClick={applyFilter}>
        <MagnifyingGlass />
        搜索
      </Button>
      <Button variant="outline" onClick={refresh}>
        <ArrowsClockwise />
        刷新
      </Button>
    </div>
  )

  return (
    <div className="space-y-4">
      <DataTable
        columns={columns}
        rows={data?.rows ?? []}
        loading={isLoading}
        total={data?.count}
        currentPage={data?.current_page ?? page}
        totalPage={data?.total_page ?? 1}
        onPageChange={setPage}
        rowKey={(row) => String(row.id)}
        toolbar={toolbar}
        emptyText="暂无任务数据"
      />
    </div>
  )
}
