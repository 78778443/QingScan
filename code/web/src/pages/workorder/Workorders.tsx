import { useState, type ReactNode } from 'react'
import { useMutation, useQuery, useQueryClient } from '@tanstack/react-query'
import { toast } from 'sonner'
import { ArrowRightLeft, Eye, Plus, RefreshCw, Search, Trash2 } from 'lucide-react'
import { apiPage, apiPost } from '@/lib/api'
import { DataTable, type Column } from '@/components/DataTable'
import { Badge } from '@/components/ui/badge'
import { Button } from '@/components/ui/button'
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

type Row = Record<string, unknown>

function str(v: unknown): string {
  if (v === undefined || v === null || v === '') return '-'
  if (typeof v === 'object') return JSON.stringify(v)
  return String(v)
}

// ---------- 状态 / 类型元信息 ----------

const STATUS_META: Record<string, { label: string; cls: string }> = {
  pending_dispatch: { label: '待派发', cls: 'bg-muted text-muted-foreground' },
  dispatched: { label: '已派发', cls: 'bg-blue-500/15 text-blue-600 dark:text-blue-400' },
  confirmed: { label: '已确认', cls: 'bg-cyan-500/15 text-cyan-600 dark:text-cyan-400' },
  fixed_unconfirmed: { label: '修复待确认', cls: 'bg-orange-500/15 text-orange-600 dark:text-orange-400' },
  fixed_confirmed: { label: '已修复关闭', cls: 'bg-green-500/15 text-green-600 dark:text-green-400' },
}

const TYPE_META: Record<string, { label: string; cls: string }> = {
  vulnerability: { label: '漏洞', cls: 'bg-blue-500/15 text-blue-600 dark:text-blue-400' },
  system: { label: '系统', cls: 'bg-orange-500/15 text-orange-600 dark:text-orange-400' },
  other: { label: '其他', cls: 'bg-muted text-muted-foreground' },
}

const STATUS_OPTIONS = Object.entries(STATUS_META).map(([value, m]) => ({ value, label: m.label }))
const TYPE_OPTIONS = Object.entries(TYPE_META).map(([value, m]) => ({ value, label: m.label }))

function statusLabel(v: unknown): string {
  const raw = String(v ?? '').trim()
  return STATUS_META[raw]?.label ?? (raw || '-')
}

function typeLabel(v: unknown): string {
  const raw = String(v ?? '').trim()
  return TYPE_META[raw]?.label ?? (raw || '-')
}

// 详情弹窗中的 key-value 行
function DetailItem({ label, value }: { label: string; value: unknown }) {
  return (
    <div className="grid grid-cols-[90px_1fr] gap-2 py-2">
      <span className="text-muted-foreground">{label}</span>
      <span className="break-all">{str(value)}</span>
    </div>
  )
}

// ---------- 确认对话框 ----------

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

export default function Workorders() {
  const queryClient = useQueryClient()

  const [page, setPage] = useState(1)
  const [keyword, setKeyword] = useState('')
  const [keywordDraft, setKeywordDraft] = useState('')
  const [status, setStatus] = useState('')
  const [type, setType] = useState('')

  const [addOpen, setAddOpen] = useState(false)
  const [form, setForm] = useState({ title: '', type: 'vulnerability', content: '', vul_id: '' })
  const [detail, setDetail] = useState<Row | null>(null)
  const [delRow, setDelRow] = useState<Row | null>(null)

  // ---------- 数据请求 ----------

  const { data, isLoading, isFetching, refetch } = useQuery({
    queryKey: ['workorders', page, keyword, status, type],
    queryFn: () =>
      apiPage<Row>('/workorder/list', {
        page,
        limit: 10,
        keyword: keyword || undefined,
        status: status || undefined,
        type: type || undefined,
      }),
  })

  const invalidate = () => {
    queryClient.invalidateQueries({ queryKey: ['workorders'] })
  }

  // ---------- 变更操作 ----------

  const addMutation = useMutation({
    mutationFn: (payload: { title: string; type: string; content: string; vul_id?: string }) =>
      apiPost('/workorder/add', payload),
    onSuccess: () => {
      invalidate()
      toast.success('工单创建成功')
      setAddOpen(false)
      setForm({ title: '', type: 'vulnerability', content: '', vul_id: '' })
    },
    onError: (err) => toast.error(err instanceof Error ? err.message : '创建工单失败'),
  })

  const statusMutation = useMutation({
    mutationFn: (payload: { id: number; status: string }) => apiPost('/workorder/status', payload),
    onSuccess: (_data, vars) => {
      invalidate()
      toast.success(`状态已流转为「${STATUS_META[vars.status]?.label ?? vars.status}」`)
    },
    onError: (err) => toast.error(err instanceof Error ? err.message : '状态流转失败'),
  })

  const delMutation = useMutation({
    mutationFn: (id: number) => apiPost('/workorder/del', { id }),
    onSuccess: () => {
      invalidate()
      setDelRow(null)
      toast.success('工单已删除')
    },
    onError: (err) => toast.error(err instanceof Error ? err.message : '删除失败'),
  })

  const handleAdd = () => {
    if (!form.title.trim() || !form.content.trim()) return
    addMutation.mutate({
      title: form.title.trim(),
      type: form.type,
      content: form.content.trim(),
      vul_id: form.vul_id.trim() || undefined,
    })
  }

  const applyFilter = () => {
    setKeyword(keywordDraft.trim())
    setPage(1)
  }

  // ---------- 表格列 ----------

  const columns: Column<Row>[] = [
    { key: 'id', header: 'ID', className: 'w-16' },
    {
      key: 'title',
      header: '标题',
      render: (row) => (
        <span className="block max-w-56 truncate font-medium" title={str(row.title)}>
          {str(row.title)}
        </span>
      ),
    },
    {
      key: 'type',
      header: '类型',
      render: (row) => {
        const m = TYPE_META[String(row.type ?? '').trim()]
        return <Badge className={m ? m.cls : 'bg-muted text-muted-foreground'}>{typeLabel(row.type)}</Badge>
      },
    },
    {
      key: 'status',
      header: '状态',
      render: (row) => {
        const m = STATUS_META[String(row.status ?? '').trim()]
        return <Badge className={m ? m.cls : 'bg-muted text-muted-foreground'}>{statusLabel(row.status)}</Badge>
      },
    },
    { key: 'vuln_name', header: '关联漏洞', render: (row) => str(row.vuln_name) },
    { key: 'creator_name', header: '创建人', render: (row) => str(row.creator_name) },
    { key: 'updated_at', header: '更新时间', className: 'whitespace-nowrap' },
    {
      key: 'actions',
      header: '操作',
      className: 'w-32',
      render: (row) => (
        <div className="flex items-center gap-1">
          <Button variant="ghost" size="icon-sm" title="详情" onClick={() => setDetail(row)}>
            <Eye className="size-4" />
          </Button>
          <DropdownMenu>
            <DropdownMenuTrigger asChild>
              <Button variant="ghost" size="icon-sm" title="状态流转">
                <ArrowRightLeft className="size-4" />
              </Button>
            </DropdownMenuTrigger>
            <DropdownMenuContent align="end">
              <DropdownMenuLabel>流转到</DropdownMenuLabel>
              {STATUS_OPTIONS.filter((s) => s.value !== String(row.status ?? '')).map((s) => (
                <DropdownMenuItem
                  key={s.value}
                  onClick={() => statusMutation.mutate({ id: Number(row.id), status: s.value })}
                >
                  {s.label}
                </DropdownMenuItem>
              ))}
            </DropdownMenuContent>
          </DropdownMenu>
          <Button variant="ghost" size="icon-sm" title="删除" onClick={() => setDelRow(row)}>
            <Trash2 className="size-4 text-destructive" />
          </Button>
        </div>
      ),
    },
  ]

  // ---------- 筛选栏 ----------

  const toolbar = (
    <div className="flex flex-wrap items-center gap-2 rounded-lg border bg-card p-3">
      <Input
        placeholder="搜索标题 / 内容"
        value={keywordDraft}
        onChange={(e) => setKeywordDraft(e.target.value)}
        onKeyDown={(e) => {
          if (e.key === 'Enter') applyFilter()
        }}
        className="h-8 w-56"
      />
      <Select
        value={status}
        onValueChange={(v) => {
          setStatus(v)
          setPage(1)
        }}
      >
        <SelectTrigger className="h-8 w-32">
          <SelectValue placeholder="工单状态" />
        </SelectTrigger>
        <SelectContent>
          <SelectItem value="">全部状态</SelectItem>
          {STATUS_OPTIONS.map((o) => (
            <SelectItem key={o.value} value={o.value}>
              {o.label}
            </SelectItem>
          ))}
        </SelectContent>
      </Select>
      <Select
        value={type}
        onValueChange={(v) => {
          setType(v)
          setPage(1)
        }}
      >
        <SelectTrigger className="h-8 w-28">
          <SelectValue placeholder="工单类型" />
        </SelectTrigger>
        <SelectContent>
          <SelectItem value="">全部类型</SelectItem>
          {TYPE_OPTIONS.map((o) => (
            <SelectItem key={o.value} value={o.value}>
              {o.label}
            </SelectItem>
          ))}
        </SelectContent>
      </Select>
      <Button variant="outline" className="h-8" onClick={applyFilter}>
        <Search className="size-4" />
        搜索
      </Button>
      <Button variant="outline" className="h-8" onClick={() => refetch()} disabled={isFetching}>
        <RefreshCw className={cn('size-4', isFetching && 'animate-spin')} />
        刷新
      </Button>
      <div className="flex-1" />
      <Button className="h-8" onClick={() => setAddOpen(true)}>
        <Plus className="size-4" />
        新建工单
      </Button>
    </div>
  )

  return (
    <div className="space-y-4">
      <div className="mb-4">
        <h1 className="text-lg font-semibold">工单管理</h1>
        <p className="text-xs text-muted-foreground">安全运营工单 - 创建、流转与关闭</p>
      </div>

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
        emptyText="暂无工单数据"
      />

      {/* 新建工单 */}
      <Dialog
        open={addOpen}
        onOpenChange={(v) => {
          if (!v && !addMutation.isPending) setAddOpen(false)
        }}
      >
        <DialogContent className="sm:max-w-md">
          <DialogHeader>
            <DialogTitle>新建工单</DialogTitle>
            <DialogDescription>创建安全运营工单，可关联漏洞编号</DialogDescription>
          </DialogHeader>
          <div className="space-y-4">
            <div className="space-y-1.5">
              <Label>标题 *</Label>
              <Input
                placeholder="工单标题"
                value={form.title}
                onChange={(e) => setForm({ ...form, title: e.target.value })}
              />
            </div>
            <div className="space-y-1.5">
              <Label>类型 *</Label>
              <Select value={form.type} onValueChange={(v) => setForm({ ...form, type: v })}>
                <SelectTrigger className="w-full">
                  <SelectValue placeholder="选择类型" />
                </SelectTrigger>
                <SelectContent>
                  {TYPE_OPTIONS.map((o) => (
                    <SelectItem key={o.value} value={o.value}>
                      {o.label}
                    </SelectItem>
                  ))}
                </SelectContent>
              </Select>
            </div>
            <div className="space-y-1.5">
              <Label>内容 *</Label>
              <Textarea
                rows={4}
                placeholder="工单内容描述"
                value={form.content}
                onChange={(e) => setForm({ ...form, content: e.target.value })}
              />
            </div>
            <div className="space-y-1.5">
              <Label>关联漏洞 ID（可选）</Label>
              <Input
                placeholder="如 1024"
                value={form.vul_id}
                onChange={(e) => setForm({ ...form, vul_id: e.target.value })}
              />
            </div>
          </div>
          <DialogFooter>
            <Button variant="outline" disabled={addMutation.isPending} onClick={() => setAddOpen(false)}>
              取消
            </Button>
            <Button
              disabled={addMutation.isPending || !form.title.trim() || !form.content.trim()}
              onClick={handleAdd}
            >
              {addMutation.isPending ? '创建中...' : '创建'}
            </Button>
          </DialogFooter>
        </DialogContent>
      </Dialog>

      {/* 工单详情 */}
      <Dialog
        open={detail !== null}
        onOpenChange={(v) => {
          if (!v) setDetail(null)
        }}
      >
        <DialogContent className="sm:max-w-lg">
          <DialogHeader>
            <DialogTitle>工单详情</DialogTitle>
            <DialogDescription>{detail ? `工单 #${str(detail.id)}` : ''}</DialogDescription>
          </DialogHeader>
          <div className="divide-y">
            <DetailItem label="标题" value={detail?.title} />
            <DetailItem label="类型" value={detail ? typeLabel(detail.type) : ''} />
            <DetailItem label="状态" value={detail ? statusLabel(detail.status) : ''} />
            <DetailItem label="内容" value={detail?.content} />
            <DetailItem label="关联漏洞" value={detail?.vuln_name} />
            <DetailItem label="漏洞ID" value={detail?.vul_id} />
            <DetailItem label="漏洞类型" value={detail?.vul_type} />
            <DetailItem label="指派给" value={detail?.assigned_to} />
            <DetailItem label="创建人" value={detail?.creator_name} />
            <DetailItem label="安全负责人" value={detail?.security_owner} />
            <DetailItem label="业务负责人" value={detail?.business_owner} />
            <DetailItem label="确认人" value={detail?.confirmer} />
            <DetailItem label="创建时间" value={detail?.created_at} />
            <DetailItem label="更新时间" value={detail?.updated_at} />
          </div>
        </DialogContent>
      </Dialog>

      {/* 删除工单 */}
      <ConfirmDialog
        open={delRow !== null}
        title="删除工单"
        description={delRow ? `确定删除工单「${str(delRow.title)}」？删除后不可恢复。` : ''}
        confirmText="删除"
        loading={delMutation.isPending}
        onConfirm={() => {
          if (delRow) delMutation.mutate(Number(delRow.id))
        }}
        onCancel={() => setDelRow(null)}
      />
    </div>
  )
}
