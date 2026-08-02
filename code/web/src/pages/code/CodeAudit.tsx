import { useEffect, useState, type ReactNode } from 'react'
import { useMutation, useQuery, useQueryClient } from '@tanstack/react-query'
import { toast } from 'sonner'
import { Eye, Plus, RefreshCw, Search, Trash2 } from 'lucide-react'
import { apiPage, apiPost } from '@/lib/api'
import { DataTable, severityColor, type Column } from '@/components/DataTable'
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
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import {
  Select,
  SelectContent,
  SelectItem,
  SelectTrigger,
  SelectValue,
} from '@/components/ui/select'
import { Tabs, TabsContent, TabsList, TabsTrigger } from '@/components/ui/tabs'
import { Textarea } from '@/components/ui/textarea'
import { cn } from '@/lib/utils'

type Row = Record<string, unknown>

function str(v: unknown): string {
  if (v === undefined || v === null || v === '') return '-'
  if (typeof v === 'object') return JSON.stringify(v)
  return String(v)
}

// 审计时间：默认值 2000-01-01 表示尚未审计，展示为 "-"
function scanTime(v: unknown): string {
  const s = str(v)
  return s.startsWith('2000-01-01') ? '-' : s
}

// 审计级别：error → 严重(红) / warning → 中危(黄)
function severityInfo(v: unknown): { label: string; cls: string } {
  const raw = String(v ?? '').trim().toLowerCase()
  if (raw === 'error') return { label: 'error', cls: severityColor('critical') }
  if (raw === 'warning') return { label: 'warning', cls: severityColor('medium') }
  return { label: raw || '-', cls: 'bg-muted text-muted-foreground' }
}

// 详情弹窗中的 key-value 行
function DetailItem({ label, value }: { label: string; value: unknown }) {
  return (
    <div className="grid grid-cols-[80px_1fr] gap-2 py-2">
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

export default function CodeAudit() {
  const queryClient = useQueryClient()

  const [tab, setTab] = useState('projects')

  // 代码项目 Tab 筛选
  const [projPage, setProjPage] = useState(1)
  const [projKeyword, setProjKeyword] = useState('')
  const [projKeywordDraft, setProjKeywordDraft] = useState('')

  // 审计结果 Tab 筛选
  const [auditPage, setAuditPage] = useState(1)
  const [auditKeyword, setAuditKeyword] = useState('')
  const [auditKeywordDraft, setAuditKeywordDraft] = useState('')
  const [severity, setSeverity] = useState('')
  const [codeId, setCodeId] = useState('')

  // 弹窗状态
  const [addOpen, setAddOpen] = useState(false)
  const [form, setForm] = useState({ name: '', ssh_url: '', desc: '' })
  const [delProject, setDelProject] = useState<Row | null>(null)
  const [detail, setDetail] = useState<Row | null>(null)

  // 切换 Tab 时重置分页与筛选
  useEffect(() => {
    setProjPage(1)
    setProjKeyword('')
    setProjKeywordDraft('')
    setAuditPage(1)
    setAuditKeyword('')
    setAuditKeywordDraft('')
    setSeverity('')
    setCodeId('')
  }, [tab])

  const invalidateAll = () => {
    queryClient.invalidateQueries({ queryKey: ['code-projects'] })
    queryClient.invalidateQueries({ queryKey: ['code-project-options'] })
    queryClient.invalidateQueries({ queryKey: ['code-audits'] })
  }

  // ---------- 数据请求 ----------

  const projectsQuery = useQuery({
    queryKey: ['code-projects', projPage, projKeyword],
    queryFn: () =>
      apiPage<Row>('/code/project_list', {
        page: projPage,
        limit: 10,
        keyword: projKeyword || undefined,
      }),
  })

  // 审计结果 Tab 的项目下拉选项
  const projectOptionsQuery = useQuery({
    queryKey: ['code-project-options'],
    queryFn: () => apiPage<Row>('/code/project_list', { page: 1, limit: 100 }),
  })

  const auditsQuery = useQuery({
    queryKey: ['code-audits', auditPage, auditKeyword, severity, codeId],
    queryFn: () =>
      apiPage<Row>('/code/audit_list', {
        page: auditPage,
        limit: 10,
        keyword: auditKeyword || undefined,
        severity: severity || undefined,
        code_id: codeId || undefined,
      }),
  })

  // ---------- 变更操作 ----------

  const addMutation = useMutation({
    mutationFn: (payload: { name: string; ssh_url: string; desc?: string }) =>
      apiPost('/code/project_add', payload),
    onSuccess: () => {
      invalidateAll()
      toast.success('新增代码项目成功')
      setAddOpen(false)
      setForm({ name: '', ssh_url: '', desc: '' })
    },
    onError: (err) => toast.error(err instanceof Error ? err.message : '新增项目失败'),
  })

  const delMutation = useMutation({
    mutationFn: (id: number) => apiPost('/code/project_del', { id }),
    onSuccess: () => {
      invalidateAll()
      setDelProject(null)
      toast.success('删除项目成功')
    },
    onError: (err) => toast.error(err instanceof Error ? err.message : '删除失败'),
  })

  const handleAdd = () => {
    if (!form.name.trim() || !form.ssh_url.trim()) return
    addMutation.mutate({
      name: form.name.trim(),
      ssh_url: form.ssh_url.trim(),
      desc: form.desc.trim() || undefined,
    })
  }

  const applyProjectFilter = () => {
    setProjKeyword(projKeywordDraft.trim())
    setProjPage(1)
  }

  const applyAuditFilter = () => {
    setAuditKeyword(auditKeywordDraft.trim())
    setAuditPage(1)
  }

  // ---------- 表格列 ----------

  const projectColumns: Column<Row>[] = [
    { key: 'id', header: 'ID', className: 'w-16' },
    { key: 'name', header: '项目名', render: (row) => <span className="font-medium">{str(row.name)}</span> },
    {
      key: 'ssh_url',
      header: '仓库地址',
      className: 'max-w-64 truncate',
      render: (row) => (
        <span className="block max-w-64 truncate font-mono text-muted-foreground" title={str(row.ssh_url)}>
          {str(row.ssh_url)}
        </span>
      ),
    },
    {
      key: 'semgrep_scan_time',
      header: '审计时间',
      className: 'whitespace-nowrap',
      render: (row) => scanTime(row.semgrep_scan_time),
    },
    {
      key: 'audit_num',
      header: '结果数',
      render: (row) => {
        const n = Number(row.audit_num)
        const num = Number.isFinite(n) ? n : 0
        return (
          <Badge className={num > 0 ? severityColor('high') : 'bg-muted text-muted-foreground'}>{num}</Badge>
        )
      },
    },
    {
      key: 'actions',
      header: '操作',
      className: 'w-16',
      render: (row) => (
        <Button variant="ghost" size="icon-sm" title="删除项目" onClick={() => setDelProject(row)}>
          <Trash2 className="size-4 text-destructive" />
        </Button>
      ),
    },
  ]

  const auditColumns: Column<Row>[] = [
    { key: 'id', header: 'ID', className: 'w-16' },
    {
      key: 'project_name',
      header: '项目',
      render: (row) => <Badge className="bg-primary/10 text-primary">{str(row.project_name)}</Badge>,
    },
    {
      key: 'file',
      header: '文件',
      className: 'max-w-56 truncate',
      render: (row) => (
        <span className="block max-w-56 truncate font-mono text-muted-foreground" title={str(row.file)}>
          {str(row.file)}
        </span>
      ),
    },
    { key: 'line', header: '行号', className: 'w-16', render: (row) => str(row.line) },
    {
      key: 'rule_id',
      header: '规则',
      className: 'max-w-36 truncate',
      render: (row) => (
        <span className="block max-w-36 truncate font-mono" title={str(row.rule_id)}>
          {str(row.rule_id)}
        </span>
      ),
    },
    {
      key: 'message',
      header: '问题',
      className: 'max-w-56 truncate',
      render: (row) => (
        <span className="block max-w-56 truncate" title={str(row.message)}>
          {str(row.message)}
        </span>
      ),
    },
    {
      key: 'severity',
      header: '级别',
      render: (row) => {
        const info = severityInfo(row.severity)
        return <Badge className={info.cls}>{info.label}</Badge>
      },
    },
    { key: 'create_time', header: '时间', className: 'whitespace-nowrap' },
    {
      key: 'actions',
      header: '操作',
      className: 'w-16',
      render: (row) => (
        <Button variant="ghost" size="icon-sm" title="查看详情" onClick={() => setDetail(row)}>
          <Eye className="size-4" />
        </Button>
      ),
    },
  ]

  // ---------- 筛选栏 ----------

  const projectsToolbar = (
    <div className="flex flex-wrap items-center gap-2 rounded-lg border bg-card p-3">
      <Input
        placeholder="搜索项目名 / 仓库地址"
        value={projKeywordDraft}
        onChange={(e) => setProjKeywordDraft(e.target.value)}
        onKeyDown={(e) => {
          if (e.key === 'Enter') applyProjectFilter()
        }}
        className="h-8 w-56"
      />
      <Button variant="outline" className="h-8" onClick={applyProjectFilter}>
        <Search className="size-4" />
        搜索
      </Button>
      <Button variant="outline" className="h-8" onClick={() => projectsQuery.refetch()} disabled={projectsQuery.isFetching}>
        <RefreshCw className={cn('size-4', projectsQuery.isFetching && 'animate-spin')} />
        刷新
      </Button>
      <div className="flex-1" />
      <Button className="h-8" onClick={() => setAddOpen(true)}>
        <Plus className="size-4" />
        新增项目
      </Button>
    </div>
  )

  const auditsToolbar = (
    <div className="flex flex-wrap items-center gap-2 rounded-lg border bg-card p-3">
      <Input
        placeholder="搜索规则 / 问题"
        value={auditKeywordDraft}
        onChange={(e) => setAuditKeywordDraft(e.target.value)}
        onKeyDown={(e) => {
          if (e.key === 'Enter') applyAuditFilter()
        }}
        className="h-8 w-52"
      />
      <Select
        value={severity}
        onValueChange={(v) => {
          setSeverity(v)
          setAuditPage(1)
        }}
      >
        <SelectTrigger className="h-8 w-28">
          <SelectValue placeholder="级别" />
        </SelectTrigger>
        <SelectContent>
          <SelectItem value="">全部级别</SelectItem>
          <SelectItem value="error">error</SelectItem>
          <SelectItem value="warning">warning</SelectItem>
        </SelectContent>
      </Select>
      <Select
        value={codeId}
        onValueChange={(v) => {
          setCodeId(v)
          setAuditPage(1)
        }}
      >
        <SelectTrigger className="h-8 w-40">
          <SelectValue placeholder="代码项目" />
        </SelectTrigger>
        <SelectContent>
          <SelectItem value="">全部项目</SelectItem>
          {projectOptionsQuery.data?.rows.map((p) => (
            <SelectItem key={String(p.id)} value={String(p.id)}>
              {str(p.name)}
            </SelectItem>
          ))}
        </SelectContent>
      </Select>
      <Button variant="outline" className="h-8" onClick={applyAuditFilter}>
        <Search className="size-4" />
        搜索
      </Button>
      <Button variant="outline" className="h-8" onClick={() => auditsQuery.refetch()} disabled={auditsQuery.isFetching}>
        <RefreshCw className={cn('size-4', auditsQuery.isFetching && 'animate-spin')} />
        刷新
      </Button>
    </div>
  )

  return (
    <div className="space-y-4">
      <div className="mb-4">
        <h1 className="text-lg font-semibold">代码审计</h1>
        <p className="text-xs text-muted-foreground">代码项目 - Semgrep 静态审计结果</p>
      </div>

      <Tabs value={tab} onValueChange={setTab}>
        <TabsList>
          <TabsTrigger value="projects">代码项目</TabsTrigger>
          <TabsTrigger value="audits">审计结果</TabsTrigger>
        </TabsList>
        <TabsContent value="projects">
          <DataTable
            columns={projectColumns}
            rows={projectsQuery.data?.rows ?? []}
            loading={projectsQuery.isLoading}
            total={projectsQuery.data?.count}
            currentPage={projectsQuery.data?.current_page ?? projPage}
            totalPage={projectsQuery.data?.total_page ?? 1}
            onPageChange={setProjPage}
            rowKey={(row) => String(row.id)}
            toolbar={projectsToolbar}
            emptyText="暂无代码项目"
          />
        </TabsContent>
        <TabsContent value="audits">
          <DataTable
            columns={auditColumns}
            rows={auditsQuery.data?.rows ?? []}
            loading={auditsQuery.isLoading}
            total={auditsQuery.data?.count}
            currentPage={auditsQuery.data?.current_page ?? auditPage}
            totalPage={auditsQuery.data?.total_page ?? 1}
            onPageChange={setAuditPage}
            rowKey={(row) => String(row.id)}
            toolbar={auditsToolbar}
            emptyText="暂无审计结果"
          />
        </TabsContent>
      </Tabs>

      {/* 新增代码项目 */}
      <Dialog
        open={addOpen}
        onOpenChange={(v) => {
          if (!v && !addMutation.isPending) setAddOpen(false)
        }}
      >
        <DialogContent className="sm:max-w-md">
          <DialogHeader>
            <DialogTitle>新增代码项目</DialogTitle>
            <DialogDescription>录入 Git 仓库信息，保存后可下发 Semgrep 审计</DialogDescription>
          </DialogHeader>
          <div className="space-y-4">
            <div className="space-y-1.5">
              <Label>项目名称 *</Label>
              <Input
                placeholder="如 qingscan-web"
                value={form.name}
                onChange={(e) => setForm({ ...form, name: e.target.value })}
              />
            </div>
            <div className="space-y-1.5">
              <Label>仓库地址 *</Label>
              <Input
                placeholder="ssh://git@host/repo.git"
                className="font-mono"
                value={form.ssh_url}
                onChange={(e) => setForm({ ...form, ssh_url: e.target.value })}
              />
            </div>
            <div className="space-y-1.5">
              <Label>项目描述</Label>
              <Textarea
                rows={3}
                placeholder="可选"
                value={form.desc}
                onChange={(e) => setForm({ ...form, desc: e.target.value })}
              />
            </div>
          </div>
          <DialogFooter>
            <Button variant="outline" disabled={addMutation.isPending} onClick={() => setAddOpen(false)}>
              取消
            </Button>
            <Button
              disabled={addMutation.isPending || !form.name.trim() || !form.ssh_url.trim()}
              onClick={handleAdd}
            >
              {addMutation.isPending ? '保存中...' : '保存'}
            </Button>
          </DialogFooter>
        </DialogContent>
      </Dialog>

      {/* 删除代码项目 */}
      <ConfirmDialog
        open={delProject !== null}
        title="删除代码项目"
        description={
          delProject ? `确定删除代码项目「${str(delProject.name)}」？其审计结果将一并删除，且不可恢复。` : ''
        }
        confirmText="删除"
        loading={delMutation.isPending}
        onConfirm={() => {
          if (delProject) delMutation.mutate(Number(delProject.id))
        }}
        onCancel={() => setDelProject(null)}
      />

      {/* 审计结果详情 */}
      <Dialog
        open={detail !== null}
        onOpenChange={(v) => {
          if (!v) setDetail(null)
        }}
      >
        <DialogContent className="sm:max-w-lg">
          <DialogHeader>
            <DialogTitle>审计结果详情</DialogTitle>
            <DialogDescription>代码审计发现的具体问题信息</DialogDescription>
          </DialogHeader>
          <div className="divide-y">
            <DetailItem label="项目" value={detail?.project_name} />
            <DetailItem label="文件" value={detail?.file} />
            <DetailItem label="行号" value={detail?.line} />
            <DetailItem label="规则" value={detail?.rule_id} />
            <DetailItem label="问题" value={detail?.message} />
            <DetailItem label="级别" value={detail?.severity} />
            <DetailItem label="时间" value={detail?.create_time} />
          </div>
        </DialogContent>
      </Dialog>
    </div>
  )
}
