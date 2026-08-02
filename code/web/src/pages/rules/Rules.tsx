import { useEffect, useState, type ReactNode } from 'react'
import { useMutation, useQuery, useQueryClient } from '@tanstack/react-query'
import { toast } from 'sonner'
import { Plus, Trash2 } from 'lucide-react'
import { apiGet, apiPost } from '@/lib/api'
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
import { Tabs, TabsList, TabsTrigger } from '@/components/ui/tabs'
import { Textarea } from '@/components/ui/textarea'

type Rule = Record<string, unknown>

interface FieldDef {
  key: string
  label: string
  type: string
  required?: boolean
}

interface RulesListData {
  type: string
  fields: FieldDef[]
  builtin: Rule[]
  custom: Rule[]
}

type Row = Rule & { _source?: 'builtin' | 'custom' }

// ---------- 规则类型元信息 ----------

const TYPE_LABELS: Record<string, string> = {
  code_audit: '代码审计',
  code_audit_taint: '污点分析',
  fingerprint: '指纹识别',
  vuln: '漏洞检测',
  waf: 'WAF识别',
}

const FALLBACK_TYPES = Object.keys(TYPE_LABELS)

// 各类型"最像名称"的字段
const NAME_KEYS: Record<string, string> = {
  code_audit: 'id',
  code_audit_taint: 'id',
  fingerprint: 'name',
  vuln: 'name',
  waf: 'firewall',
}

const NAME_CANDIDATES = ['id', 'name', 'firewall', 'title']

// 关键内容展示优先级
const CONTENT_KEYS = ['pattern', 'paths', 'keywords', 'sinks', 'regex', 'headers', 'body', 'description', 'detail']

// 字段说明文案
const FIELD_HINTS: Record<string, string> = {
  pattern: '正则表达式，如 #\\beval\\s*\\(#i',
  sinks: '函数名，逗号分隔',
  paths: '路径，逗号分隔',
  keywords: '关键字，逗号分隔',
  headers: 'key=value，逗号分隔，值可空',
}

// select 字段的可选值
const SELECT_OPTIONS: Record<string, string[]> = {
  severity: ['error', 'warning'],
}

// ---------- 工具函数 ----------

function ruleName(rule: Rule, type: string): string {
  const primary = NAME_KEYS[type]
  if (primary && rule[primary] !== undefined && rule[primary] !== null && rule[primary] !== '') {
    return String(rule[primary])
  }
  for (const k of NAME_CANDIDATES) {
    if (rule[k] !== undefined && rule[k] !== null && rule[k] !== '') return String(rule[k])
  }
  return '-'
}

function contentOf(rule: Rule): string {
  const parts: string[] = []
  for (const k of CONTENT_KEYS) {
    const v = rule[k]
    if (v === undefined || v === null || v === '') continue
    parts.push(Array.isArray(v) ? v.join(', ') : String(v))
  }
  return parts.join(' · ')
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

// ---------- 动态表单字段 ----------

function FieldControl({
  f,
  value,
  onChange,
}: {
  f: FieldDef
  value: string
  onChange: (v: string) => void
}) {
  const hint = FIELD_HINTS[f.key]
  const label = (
    <Label>
      {f.label}
      {f.required ? ' *' : ''}
    </Label>
  )
  const hintNode = hint ? <p className="text-xs text-muted-foreground">{hint}</p> : null

  if (f.type === 'textarea') {
    return (
      <div className="space-y-1.5">
        {label}
        <Textarea rows={3} placeholder={hint} value={value} onChange={(e) => onChange(e.target.value)} />
        {hintNode}
      </div>
    )
  }

  if (f.type === 'select') {
    const options = SELECT_OPTIONS[f.key]
    if (options) {
      return (
        <div className="space-y-1.5">
          {label}
          <Select value={value} onValueChange={onChange}>
            <SelectTrigger className="w-full">
              <SelectValue placeholder={`请选择${f.label}`} />
            </SelectTrigger>
            <SelectContent>
              {options.map((o) => (
                <SelectItem key={o} value={o}>
                  {o}
                </SelectItem>
              ))}
            </SelectContent>
          </Select>
          {hintNode}
        </div>
      )
    }
  }

  return (
    <div className="space-y-1.5">
      {label}
      <Input placeholder={hint} value={value} onChange={(e) => onChange(e.target.value)} />
      {hintNode}
    </div>
  )
}

// ---------- 页面组件 ----------

export default function Rules() {
  const queryClient = useQueryClient()

  const [activeType, setActiveType] = useState('vuln')
  const [addOpen, setAddOpen] = useState(false)
  const [form, setForm] = useState<Record<string, string>>({})
  const [delRow, setDelRow] = useState<Row | null>(null)

  // ---------- 数据请求 ----------

  const { data: typesData } = useQuery({
    queryKey: ['rules-types'],
    queryFn: () => apiGet<string[]>('/rules/types'),
    retry: false,
  })
  const types = typesData && typesData.length > 0 ? typesData : FALLBACK_TYPES

  useEffect(() => {
    if (types.length > 0 && !types.includes(activeType)) setActiveType(types[0])
  }, [types, activeType])

  useEffect(() => {
    setForm({})
  }, [activeType])

  const { data: listData, isLoading } = useQuery({
    queryKey: ['rules-list', activeType],
    queryFn: () => apiGet<RulesListData>('/rules/list', { type: activeType }),
  })

  const fields = listData?.fields ?? []
  const rows: Row[] = [
    ...(listData?.builtin ?? []).map((r) => ({ ...r, _source: 'builtin' as const })),
    ...(listData?.custom ?? []).map((r) => ({ ...r, _source: 'custom' as const })),
  ]

  const invalidate = () => {
    queryClient.invalidateQueries({ queryKey: ['rules-list'] })
  }

  // ---------- 变更操作 ----------

  const saveMutation = useMutation({
    mutationFn: (payload: { type: string; rule: Record<string, string> }) => apiPost('/rules/save', payload),
    onSuccess: () => {
      invalidate()
      toast.success('自定义规则已保存')
      setAddOpen(false)
      setForm({})
    },
    onError: (err) => toast.error(err instanceof Error ? err.message : '保存失败'),
  })

  const delMutation = useMutation({
    mutationFn: (payload: { type: string; id: string }) => apiPost('/rules/delete', payload),
    onSuccess: () => {
      invalidate()
      setDelRow(null)
      toast.success('规则已删除')
    },
    onError: (err) => toast.error(err instanceof Error ? err.message : '删除失败'),
  })

  const formValid = fields.every((f) => !f.required || String(form[f.key] ?? '').trim() !== '')

  const handleSave = () => {
    if (!formValid) return
    saveMutation.mutate({ type: activeType, rule: form })
  }

  // ---------- 表格列 ----------

  const isAudit = activeType === 'code_audit' || activeType === 'code_audit_taint'

  const columns: Column<Row>[] = [
    {
      key: 'name',
      header: '规则ID/名称',
      render: (row) => (
        <span className="block max-w-56 truncate font-medium" title={ruleName(row, activeType)}>
          {ruleName(row, activeType)}
        </span>
      ),
    },
    {
      key: 'content',
      header: '关键内容',
      render: (row) => {
        const c = contentOf(row)
        return (
          <span className="block max-w-md truncate text-muted-foreground" title={c}>
            {c || '-'}
          </span>
        )
      },
    },
    {
      key: 'severity',
      header: '级别',
      className: 'w-20',
      render: (row) => {
        if (!isAudit) return '-'
        const raw = String(row.severity ?? '').trim().toLowerCase()
        if (!raw) return '-'
        const m =
          raw === 'error'
            ? { label: 'error', cls: severityColor('critical') }
            : raw === 'warning'
              ? { label: 'warning', cls: severityColor('medium') }
              : { label: raw, cls: 'bg-muted text-muted-foreground' }
        return <Badge className={m.cls}>{m.label}</Badge>
      },
    },
    {
      key: 'source',
      header: '来源',
      className: 'w-20',
      render: (row) =>
        row._source === 'custom' ? (
          <Badge className="bg-blue-500/15 text-blue-600 dark:text-blue-400">自定义</Badge>
        ) : (
          <Badge className="bg-muted text-muted-foreground">内置</Badge>
        ),
    },
    {
      key: 'actions',
      header: '操作',
      className: 'w-24',
      render: (row) =>
        row._source === 'custom' ? (
          <Button variant="ghost" size="icon-sm" title="删除" onClick={() => setDelRow(row)}>
            <Trash2 className="size-4 text-destructive" />
          </Button>
        ) : (
          <span className="text-muted-foreground">-</span>
        ),
    },
  ]

  // ---------- 渲染 ----------

  const toolbar = (
    <div className="text-xs text-muted-foreground">
      内置 {listData?.builtin?.length ?? 0} 条 · 自定义 {listData?.custom?.length ?? 0} 条
    </div>
  )

  return (
    <div className="space-y-4">
      {/* 规则说明 */}
      <div className="rounded-lg border bg-card p-3 text-xs text-muted-foreground">
        自定义规则存放在 extend/rules/ 目录，与内置规则自动合并，无需修改代码
      </div>

      {/* 类型 Tabs + 新增按钮 */}
      <div className="flex flex-wrap items-center justify-between gap-3">
        <Tabs value={activeType} onValueChange={setActiveType}>
          <TabsList>
            {types.map((t) => (
              <TabsTrigger key={t} value={t}>
                {TYPE_LABELS[t] ?? t}
              </TabsTrigger>
            ))}
          </TabsList>
        </Tabs>
        <Button className="h-8" onClick={() => setAddOpen(true)}>
          <Plus className="size-4" />
          新增自定义规则
        </Button>
      </div>

      <DataTable
        columns={columns}
        rows={rows}
        loading={isLoading}
        rowKey={(row) => `${row._source ?? 'builtin'}-${ruleName(row, activeType)}`}
        toolbar={toolbar}
        emptyText="暂无规则"
      />

      {/* 新增自定义规则 */}
      <Dialog
        open={addOpen}
        onOpenChange={(v) => {
          if (!v && !saveMutation.isPending) setAddOpen(false)
        }}
      >
        <DialogContent className="sm:max-w-lg">
          <DialogHeader>
            <DialogTitle>新增自定义规则</DialogTitle>
            <DialogDescription>
              {TYPE_LABELS[activeType] ?? activeType} - 保存后自动生效
            </DialogDescription>
          </DialogHeader>
          <div className="space-y-4">
            {fields.map((f) => (
              <FieldControl
                key={f.key}
                f={f}
                value={form[f.key] ?? ''}
                onChange={(v) => setForm((prev) => ({ ...prev, [f.key]: v }))}
              />
            ))}
          </div>
          <DialogFooter>
            <Button variant="outline" disabled={saveMutation.isPending} onClick={() => setAddOpen(false)}>
              取消
            </Button>
            <Button disabled={saveMutation.isPending || !formValid} onClick={handleSave}>
              {saveMutation.isPending ? '保存中...' : '保存'}
            </Button>
          </DialogFooter>
        </DialogContent>
      </Dialog>

      {/* 删除确认 */}
      <ConfirmDialog
        open={delRow !== null}
        title="删除自定义规则"
        description={delRow ? `确定删除自定义规则「${ruleName(delRow, activeType)}」？删除后不可恢复。` : ''}
        confirmText="删除"
        loading={delMutation.isPending}
        onConfirm={() => {
          if (delRow) delMutation.mutate({ type: activeType, id: ruleName(delRow, activeType) })
        }}
        onCancel={() => setDelRow(null)}
      />
    </div>
  )
}
