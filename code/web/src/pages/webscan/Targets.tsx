import { useMemo, useState, type ReactNode } from 'react'
import { useMutation, useQuery, useQueryClient } from '@tanstack/react-query'
import { toast } from 'sonner'
import {
  ArrowsClockwiseIcon,
  DotsThreeIcon,
  EraserIcon,
  MagnifyingGlassIcon,
  PauseIcon,
  PlayIcon,
  PlusIcon,
  TrashIcon,
} from '@phosphor-icons/react'

import { apiPage, apiPost } from '@/lib/api'
import { DataTable, severityColor, type Column } from '@/components/DataTable'
import { Badge } from '@/components/ui/badge'
import { Button } from '@/components/ui/button'
import { Checkbox } from '@/components/ui/checkbox'
import {
  Dialog,
  DialogContent,
  DialogDescription,
  DialogFooter,
  DialogHeader,
  DialogTitle,
} from '@/components/ui/dialog'
import {
  DropdownMenu,
  DropdownMenuContent,
  DropdownMenuItem,
  DropdownMenuLabel,
  DropdownMenuSeparator,
  DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import {
  Select,
  SelectContent,
  SelectItem,
  SelectTrigger,
  SelectValue,
} from '@/components/ui/select'
import { Textarea } from '@/components/ui/textarea'
import { cn } from '@/lib/utils'

// ---------- 类型与常量 ----------

interface AppRow extends Record<string, unknown> {
  id: number
  name?: string
  url?: string
  status?: number | string
  is_check?: number | string
  create_time?: string
  update_time?: string
  statuscode?: string | number | null
  cms?: string
  server?: string
  is_waf?: string
}

// 新增目标时可选的扫描工具
const TOOL_OPTIONS = [
  { label: 'Nuclei 模板扫描', value: 'nuclei' },
  { label: 'Vulmap 漏洞检测', value: 'vulmap' },
  { label: 'XRay 漏洞扫描', value: 'xray' },
  { label: 'AWVS 漏洞扫描', value: 'awvs' },
  { label: 'Sqlmap 注入检测', value: 'sqlmap' },
  { label: 'Dirmap 目录扫描', value: 'dirmap' },
  { label: 'WhatWeb 指纹识别', value: 'whatweb' },
  { label: 'Rad 爬虫', value: 'rad' },
  { label: 'OneForAll 子域名', value: 'oneforall' },
  { label: 'FOFA 资产', value: 'fofa' },
  { label: 'Hydra 弱口令', value: 'hydra' },
]

// 重新扫描工具（对应后端 app_rescan 的 tools_name）
const RESCAN_TOOL_OPTIONS = [
  { label: 'XRay 漏洞扫描', value: 'xray' },
  { label: 'Nuclei 模板扫描', value: 'nucleiScan' },
  { label: 'Vulmap PoC 检测', value: 'vulmapPocTest' },
  { label: 'Sqlmap 注入检测', value: 'sqlmapScan' },
  { label: 'Dirmap 目录扫描', value: 'dirmapScan' },
  { label: 'WhatWeb 指纹识别', value: 'whatweb' },
  { label: 'AWVS 漏洞扫描', value: 'awvsScan' },
  { label: 'Rad 爬虫抓取', value: 'rad' },
  { label: 'OneForAll 子域名', value: 'subdomainScan' },
  { label: 'Hydra 弱口令', value: 'sshScan' },
  { label: '基础信息获取', value: 'getBaseInfo' },
]

// 工具结果计数展示顺序（后端返回 xray_num / sqlmap_num 等）
const TOOL_COUNT_KEYS = ['xray', 'nuclei', 'vulmap', 'sqlmap', 'awvs', 'dirmap', 'whatweb', 'oneforall', 'rad']

// ---------- 容错取值工具函数 ----------

// 工具计数：兼容 xray_num / count_xray / xray 三种字段形态
function getToolCount(row: AppRow, tool: string): number {
  const raw = row[`${tool}_num`] ?? row[`count_${tool}`] ?? row[tool] ?? 0
  const n = Number(raw)
  return Number.isFinite(n) ? n : 0
}

// 目标状态：后端 1=启用 2=暂停（兼容 0 表示暂停的旧数据）
function rowStatus(row: AppRow): number {
  const s = Number(row.status ?? row.is_check ?? 1)
  return Number.isNaN(s) ? 1 : s
}

function isPaused(row: AppRow): boolean {
  const s = rowStatus(row)
  return s === 0 || s === 2
}

function statusInfo(status: number): { label: string; className: string } {
  if (status === 1) return { label: '正常', className: severityColor('low') }
  if (status === 0 || status === 2) return { label: '暂停', className: severityColor('high') }
  return { label: '未知', className: 'bg-muted text-muted-foreground' }
}

function statuscodeInfo(code: string | number | null | undefined): { label: string; className: string } {
  if (code === null || code === undefined || code === '') {
    return { label: '-', className: 'bg-muted text-muted-foreground' }
  }
  const n = Number(code)
  const label = String(code)
  if (!Number.isFinite(n)) return { label, className: 'bg-muted text-muted-foreground' }
  if (n >= 400) return { label, className: severityColor('critical') }
  if (n >= 300) return { label, className: severityColor('medium') }
  if (n >= 200) return { label, className: severityColor('low') }
  return { label, className: 'bg-muted text-muted-foreground' }
}

// ---------- 确认对话框（删除 / 批量删除 / 清空结果共用） ----------

interface ConfirmDialogProps {
  open: boolean
  title: string
  description: ReactNode
  confirmText?: string
  loading?: boolean
  onConfirm: () => void
  onCancel: () => void
}

function ConfirmDialog({
  open,
  title,
  description,
  confirmText = '确认',
  loading = false,
  onConfirm,
  onCancel,
}: ConfirmDialogProps) {
  return (
    <Dialog
      open={open}
      onOpenChange={(v) => {
        if (!v && !loading) onCancel()
      }}
    >
      <DialogContent className="sm:max-w-md">
        <DialogHeader>
          <DialogTitle>{title}</DialogTitle>
          <DialogDescription>{description}</DialogDescription>
        </DialogHeader>
        <DialogFooter>
          <Button variant="outline" disabled={loading} onClick={onCancel}>
            取消
          </Button>
          <Button variant="destructive" disabled={loading} onClick={onConfirm}>
            {loading ? '处理中...' : confirmText}
          </Button>
        </DialogFooter>
      </DialogContent>
    </Dialog>
  )
}

// ---------- 页面组件 ----------

export default function Targets() {
  const queryClient = useQueryClient()

  // 分页与筛选（点击“查询”后才提交）
  const [page, setPage] = useState(1)
  const [keyword, setKeyword] = useState('')
  const [status, setStatus] = useState('all')
  const [statuscode, setStatuscode] = useState('all')
  const [filters, setFilters] = useState({ keyword: '', status: '', statuscode: '' })

  // 选择与对话框状态
  const [selected, setSelected] = useState<Set<number>>(new Set())
  const [addOpen, setAddOpen] = useState(false)
  const [urls, setUrls] = useState('')
  const [tools, setTools] = useState<string[]>([])
  const [delTarget, setDelTarget] = useState<AppRow | null>(null)
  const [batchDelOpen, setBatchDelOpen] = useState(false)
  const [qingkongTarget, setQingkongTarget] = useState<AppRow | null>(null)
  const [rescanTarget, setRescanTarget] = useState<AppRow | null>(null)
  const [rescanTool, setRescanTool] = useState('xray')

  const invalidateAppList = () => {
    queryClient.invalidateQueries({ queryKey: ['app_list'] })
  }

  // ---------- 列表数据 ----------

  const { data, isLoading, isFetching, refetch } = useQuery({
    queryKey: ['app_list', page, filters],
    queryFn: () =>
      apiPage<AppRow>('/api/webscan/app_list', {
        page,
        limit: 20,
        keyword: filters.keyword || undefined,
        statuscode: filters.statuscode || undefined,
        status: filters.status || undefined,
      }),
  })

  const rows = data?.rows ?? []

  // 状态码筛选选项：从当前数据中收集 + 保留已选值
  const statuscodeOptions = useMemo(() => {
    const set = new Set<string>()
    for (const r of rows) {
      const v = r.statuscode
      if (v !== null && v !== undefined && v !== '') set.add(String(v))
    }
    if (filters.statuscode) set.add(filters.statuscode)
    return Array.from(set).sort((a, b) => Number(a) - Number(b))
  }, [rows, filters.statuscode])

  const handleSearch = () => {
    setPage(1)
    setFilters({
      keyword: keyword.trim(),
      status: status === 'all' ? '' : status,
      statuscode: statuscode === 'all' ? '' : statuscode,
    })
  }

  // ---------- 变更操作 ----------

  const addMutation = useMutation({
    mutationFn: (payload: { urls: string; tools: string[] }) => apiPost('/api/webscan/app_add', payload),
    onSuccess: () => {
      invalidateAppList()
      toast.success('新增目标成功，扫描任务已下发')
      setAddOpen(false)
      setUrls('')
      setTools([])
    },
    onError: (err) => toast.error(err instanceof Error ? err.message : '新增目标失败'),
  })

  const delMutation = useMutation({
    mutationFn: (id: number) => apiPost('/api/webscan/app_del', { id }),
    onSuccess: () => {
      invalidateAppList()
      setDelTarget(null)
      setSelected((prev) => {
        const next = new Set(prev)
        next.delete(delTarget?.id ?? -1)
        return next
      })
      toast.success('删除成功')
    },
    onError: (err) => toast.error(err instanceof Error ? err.message : '删除失败'),
  })

  const batchDelMutation = useMutation({
    mutationFn: (ids: number[]) => apiPost('/api/webscan/app_batch_del', { ids }),
    onSuccess: () => {
      invalidateAppList()
      setBatchDelOpen(false)
      setSelected(new Set())
      toast.success('批量删除成功')
    },
    onError: (err) => toast.error(err instanceof Error ? err.message : '批量删除失败'),
  })

  const suspendMutation = useMutation({
    mutationFn: (id: number) => apiPost('/api/webscan/app_suspend', { id }),
    onSuccess: (data) => {
      invalidateAppList()
      const d = data as { status?: number | string } | undefined
      toast.success(d && Number(d.status) === 1 ? '扫描已启用' : '扫描已暂停')
    },
    onError: (err) => toast.error(err instanceof Error ? err.message : '操作失败'),
  })

  const qingkongMutation = useMutation({
    mutationFn: (id: number) => apiPost('/api/webscan/app_qingkong', { id }),
    onSuccess: () => {
      invalidateAppList()
      setQingkongTarget(null)
      toast.success('清空成功')
    },
    onError: (err) => toast.error(err instanceof Error ? err.message : '清空失败'),
  })

  const rescanMutation = useMutation({
    mutationFn: (payload: { id: number; tools_name: string }) => apiPost('/api/webscan/app_rescan', payload),
    onSuccess: () => {
      toast.success('重新扫描任务已下发')
      setRescanTarget(null)
    },
    onError: (err) => toast.error(err instanceof Error ? err.message : '重新扫描失败'),
  })

  // ---------- 选择逻辑 ----------

  const toggleSelect = (id: number, checked: boolean) => {
    setSelected((prev) => {
      const next = new Set(prev)
      if (checked) next.add(id)
      else next.delete(id)
      return next
    })
  }

  // ---------- 表格列 ----------

  const columns = useMemo<Column<AppRow>[]>(() => {
    const allChecked = rows.length > 0 && rows.every((r) => selected.has(r.id))
    const someChecked = rows.some((r) => selected.has(r.id))
    return [
      {
        key: 'select',
        header: (
          <Checkbox
            checked={allChecked ? true : someChecked ? 'indeterminate' : false}
            onCheckedChange={(checked) => {
              setSelected((prev) => {
                const next = new Set(prev)
                rows.forEach((r) => {
                  if (checked) next.add(r.id)
                  else next.delete(r.id)
                })
                return next
              })
            }}
          />
        ),
        className: 'w-10',
        render: (row) => (
          <Checkbox
            checked={selected.has(row.id)}
            onCheckedChange={(checked) => toggleSelect(row.id, checked === true)}
          />
        ),
      },
      {
        key: 'id',
        header: 'ID',
        className: 'w-14 text-muted-foreground',
        render: (row) => String(row.id),
      },
      {
        key: 'name',
        header: '名称',
        render: (row) => <span className="font-medium">{row.name || '-'}</span>,
      },
      {
        key: 'url',
        header: 'URL',
        render: (row) => (
          <span className="inline-block max-w-64 truncate align-middle text-muted-foreground" title={String(row.url ?? '')}>
            {row.url || '-'}
          </span>
        ),
      },
      {
        key: 'status',
        header: '状态',
        render: (row) => {
          const info = statusInfo(rowStatus(row))
          return <Badge className={info.className}>{info.label}</Badge>
        },
      },
      {
        key: 'statuscode',
        header: '状态码',
        render: (row) => {
          const info = statuscodeInfo(row.statuscode ?? null)
          return <Badge className={info.className}>{info.label}</Badge>
        },
      },
      {
        key: 'cms',
        header: 'CMS',
        render: (row) => <span className="text-muted-foreground">{row.cms || '-'}</span>,
      },
      {
        key: 'server',
        header: 'SERVER',
        render: (row) => <span className="text-muted-foreground">{row.server || '-'}</span>,
      },
      {
        key: 'tools',
        header: '工具结果',
        render: (row) => {
          const counts = TOOL_COUNT_KEYS.filter((t) => getToolCount(row, t) > 0)
          if (counts.length === 0) return <span className="text-muted-foreground">-</span>
          return (
            <div className="flex max-w-72 flex-wrap gap-1">
              {counts.map((t) => (
                <Badge key={t} className="rounded-sm bg-primary/10 px-1.5 font-mono text-xs text-primary">
                  {t}:{getToolCount(row, t)}
                </Badge>
              ))}
            </div>
          )
        },
      },
      {
        key: 'actions',
        header: '操作',
        className: 'w-20',
        render: (row) => (
          <DropdownMenu>
            <DropdownMenuTrigger asChild>
              <Button variant="ghost" size="icon-sm" className="hover:bg-accent/50" title="操作">
                <DotsThreeIcon />
              </Button>
            </DropdownMenuTrigger>
            <DropdownMenuContent align="end">
              <DropdownMenuLabel>目标操作</DropdownMenuLabel>
              <DropdownMenuItem
                onClick={() => {
                  setRescanTool('xray')
                  setRescanTarget(row)
                }}
              >
                <PlayIcon />
                重新扫描
              </DropdownMenuItem>
              <DropdownMenuItem onClick={() => suspendMutation.mutate(row.id)}>
                {isPaused(row) ? <PlayIcon /> : <PauseIcon />}
                {isPaused(row) ? '启用扫描' : '暂停扫描'}
              </DropdownMenuItem>
              <DropdownMenuSeparator />
              <DropdownMenuItem onClick={() => setQingkongTarget(row)}>
                <EraserIcon />
                清空结果
              </DropdownMenuItem>
              <DropdownMenuItem variant="destructive" onClick={() => setDelTarget(row)}>
                <TrashIcon />
                删除
              </DropdownMenuItem>
            </DropdownMenuContent>
          </DropdownMenu>
        ),
      },
    ]
  }, [rows, selected, suspendMutation])

  // ---------- 筛选与操作栏 ----------

  const toolbar = (
    <div className="flex flex-wrap items-center gap-2 rounded-lg border bg-card p-3">
      <div className="relative">
        <MagnifyingGlassIcon className="absolute top-1/2 left-2 size-4 -translate-y-1/2 text-muted-foreground" />
        <Input
          className="h-8 w-56 pl-7"
          placeholder="搜索名称 / URL"
          value={keyword}
          onChange={(e) => setKeyword(e.target.value)}
          onKeyDown={(e) => {
            if (e.key === 'Enter') handleSearch()
          }}
        />
      </div>
      <Select value={status} onValueChange={setStatus}>
        <SelectTrigger className="h-8 w-28">
          <SelectValue placeholder="状态" />
        </SelectTrigger>
        <SelectContent>
          <SelectItem value="all">全部状态</SelectItem>
          <SelectItem value="1">正常</SelectItem>
          <SelectItem value="2">暂停</SelectItem>
        </SelectContent>
      </Select>
      <Select value={statuscode} onValueChange={setStatuscode}>
        <SelectTrigger className="h-8 w-28">
          <SelectValue placeholder="状态码" />
        </SelectTrigger>
        <SelectContent>
          <SelectItem value="all">全部状态码</SelectItem>
          {statuscodeOptions.map((c) => (
            <SelectItem key={c} value={c}>
              {c}
            </SelectItem>
          ))}
        </SelectContent>
      </Select>
      <Button variant="outline" className="h-8" onClick={handleSearch}>
        查询
      </Button>
      <Button
        variant="outline"
        className="h-8"
        onClick={() => refetch()}
        disabled={isFetching}
        title="刷新"
      >
        <ArrowsClockwiseIcon className={cn('size-4', isFetching && 'animate-spin')} />
      </Button>
      <div className="flex-1" />
      {selected.size > 0 && (
        <Button variant="destructive" className="h-8" onClick={() => setBatchDelOpen(true)}>
          <TrashIcon className="size-4" />
          批量删除({selected.size})
        </Button>
      )}
      <Button
        className="h-8 bg-gradient-to-r from-primary to-blue-500 hover:opacity-90"
        onClick={() => setAddOpen(true)}
      >
        <PlusIcon className="size-4" />
        新增目标
      </Button>
    </div>
  )

  const toggleTool = (tool: string, checked: boolean) => {
    setTools((prev) =>
      checked ? Array.from(new Set([...prev, tool])) : prev.filter((t) => t !== tool),
    )
  }

  const handleAdd = () => {
    if (!urls.trim()) return
    addMutation.mutate({ urls: urls.trim(), tools })
  }

  return (
    <div className="space-y-4">
      <DataTable<AppRow>
        columns={columns}
        rows={rows}
        loading={isLoading}
        total={data?.count}
        currentPage={data?.current_page}
        totalPage={data?.total_page}
        onPageChange={setPage}
        rowKey={(row) => row.id}
        rowClassName={(row) => (selected.has(row.id) ? 'bg-primary/5' : undefined)}
        toolbar={toolbar}
        emptyText="暂无目标数据"
      />

      {/* 新增目标 */}
      <Dialog
        open={addOpen}
        onOpenChange={(v) => {
          if (!v && !addMutation.isPending) setAddOpen(false)
        }}
      >
        <DialogContent className="sm:max-w-lg">
          <DialogHeader>
            <DialogTitle>新增扫描目标</DialogTitle>
            <DialogDescription>每行填写一个 URL，可同时勾选需要执行的扫描工具</DialogDescription>
          </DialogHeader>
          <div className="space-y-4">
            <div className="space-y-1.5">
              <Label>目标 URL（每行一个）</Label>
              <Textarea
                rows={6}
                placeholder={'https://example.com\nhttp://192.168.1.1'}
                value={urls}
                onChange={(e) => setUrls(e.target.value)}
              />
            </div>
            <div className="space-y-1.5">
              <Label>扫描工具</Label>
              <div className="grid grid-cols-2 gap-x-4 gap-y-2 sm:grid-cols-3">
                {TOOL_OPTIONS.map((t) => (
                  <label key={t.value} className="flex cursor-pointer items-center gap-2 text-xs">
                    <Checkbox
                      checked={tools.includes(t.value)}
                      onCheckedChange={(checked) => toggleTool(t.value, checked === true)}
                    />
                    {t.label}
                  </label>
                ))}
              </div>
            </div>
          </div>
          <DialogFooter>
            <Button variant="outline" disabled={addMutation.isPending} onClick={() => setAddOpen(false)}>
              取消
            </Button>
            <Button disabled={addMutation.isPending || !urls.trim()} onClick={handleAdd}>
              {addMutation.isPending ? '添加中...' : '添加'}
            </Button>
          </DialogFooter>
        </DialogContent>
      </Dialog>

      {/* 删除单个目标 */}
      <ConfirmDialog
        open={delTarget !== null}
        title="删除目标"
        description={
          delTarget
            ? `确定删除目标「${delTarget.name || delTarget.url || delTarget.id}」？该操作将级联删除其所有扫描结果，且不可恢复。`
            : ''
        }
        confirmText="删除"
        loading={delMutation.isPending}
        onConfirm={() => {
          if (delTarget) delMutation.mutate(delTarget.id)
        }}
        onCancel={() => setDelTarget(null)}
      />

      {/* 批量删除 */}
      <ConfirmDialog
        open={batchDelOpen}
        title="批量删除"
        description={`确定删除选中的 ${selected.size} 个目标？该操作将级联删除其所有扫描结果，且不可恢复。`}
        confirmText="批量删除"
        loading={batchDelMutation.isPending}
        onConfirm={() => batchDelMutation.mutate(Array.from(selected))}
        onCancel={() => setBatchDelOpen(false)}
      />

      {/* 清空结果 */}
      <ConfirmDialog
        open={qingkongTarget !== null}
        title="清空结果"
        description={
          qingkongTarget
            ? `确定清空目标「${qingkongTarget.name || qingkongTarget.url || qingkongTarget.id}」的所有扫描结果？扫描时间将重置。`
            : ''
        }
        confirmText="清空"
        loading={qingkongMutation.isPending}
        onConfirm={() => {
          if (qingkongTarget) qingkongMutation.mutate(qingkongTarget.id)
        }}
        onCancel={() => setQingkongTarget(null)}
      />

      {/* 重新扫描 */}
      <Dialog
        open={rescanTarget !== null}
        onOpenChange={(v) => {
          if (!v && !rescanMutation.isPending) setRescanTarget(null)
        }}
      >
        <DialogContent className="sm:max-w-md">
          <DialogHeader>
            <DialogTitle>重新扫描</DialogTitle>
            <DialogDescription>
              为「{rescanTarget?.name || rescanTarget?.url || rescanTarget?.id}」选择要重新执行的扫描工具
            </DialogDescription>
          </DialogHeader>
          <div className="space-y-1.5">
            <Label>扫描工具</Label>
            <Select value={rescanTool} onValueChange={setRescanTool}>
              <SelectTrigger className="w-full">
                <SelectValue placeholder="选择工具" />
              </SelectTrigger>
              <SelectContent>
                {RESCAN_TOOL_OPTIONS.map((o) => (
                  <SelectItem key={o.value} value={o.value}>
                    {o.label}
                  </SelectItem>
                ))}
              </SelectContent>
            </Select>
          </div>
          <DialogFooter>
            <Button variant="outline" disabled={rescanMutation.isPending} onClick={() => setRescanTarget(null)}>
              取消
            </Button>
            <Button
              disabled={rescanMutation.isPending}
              onClick={() => {
                if (rescanTarget) rescanMutation.mutate({ id: rescanTarget.id, tools_name: rescanTool })
              }}
            >
              {rescanMutation.isPending ? '下发中...' : '重新扫描'}
            </Button>
          </DialogFooter>
        </DialogContent>
      </Dialog>
    </div>
  )
}
