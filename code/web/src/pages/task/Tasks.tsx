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

const columns: Column<Row>[] = [
  { key: 'id', header: 'ID', className: 'w-16' },
  {
    key: 'tool',
    header: '工具',
    render: (row) => (
      <Badge className="bg-blue-500/15 text-blue-600 dark:text-blue-400">{str(row.tool)}</Badge>
    ),
  },
  { key: 'target_table', header: '目标类型' },
  {
    key: 'target',
    header: '目标',
    className: 'max-w-56 truncate',
    render: (row) => (
      <span className="block max-w-56 truncate" title={str(row.target)}>
        {str(row.target)}
      </span>
    ),
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

export default function Tasks() {
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
        placeholder="工具名称"
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
      <div className="mb-4">
        <h1 className="text-lg font-semibold">扫描任务</h1>
        <p className="text-xs text-muted-foreground">任务列表 - 扫描工具执行任务记录</p>
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
        emptyText="暂无任务数据"
      />
    </div>
  )
}
