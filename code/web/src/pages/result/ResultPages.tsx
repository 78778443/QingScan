import { useEffect, useMemo, useState } from 'react'
import { useParams } from 'react-router-dom'
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
import {
  Dialog,
  DialogContent,
  DialogDescription,
  DialogHeader,
  DialogTitle,
} from '@/components/ui/dialog'
import { MagnifyingGlass, ArrowsClockwise } from '@phosphor-icons/react'

type Row = Record<string, unknown>

function str(v: unknown): string {
  if (v === undefined || v === null || v === '') return '-'
  if (typeof v === 'object') return JSON.stringify(v)
  return String(v)
}

// 危害等级：兼容中文（严重/高危/中危/低危）与数字（4/3/2/1）表示
function vulLevelText(v: unknown): string {
  const raw = String(v ?? '').trim().toLowerCase()
  const map: Record<string, string> = {
    '严重': '严重',
    critical: '严重',
    '4': '严重',
    '高危': '高危',
    high: '高危',
    '3': '高危',
    '中危': '中危',
    medium: '中危',
    '2': '中危',
    '低危': '低危',
    low: '低危',
    '1': '低危',
  }
  return map[raw] ?? (raw === '' ? '-' : String(v))
}

function VulLevelBadge({ value }: { value: unknown }) {
  const text = vulLevelText(value)
  const LEVELS = ['严重', '高危', '中危', '低危']
  if (!LEVELS.includes(text)) {
    return <Badge variant="outline">{text}</Badge>
  }
  return <Badge className={severityColor(text)}>{text}</Badge>
}

const idCol: Column<Row> = {
  key: 'id',
  header: 'ID',
  className: 'w-16',
}

const timeCol: Column<Row> = {
  key: 'create_time',
  header: '创建时间',
  className: 'whitespace-nowrap',
}

interface KindConfig {
  title: string
  endpoint: string
  // 详情弹窗字段顺序：[字段名, 中文标签]
  detailFields: [string, string][]
  columns: Column<Row>[]
}

const VALID_KINDS = ['plugin', 'vulnerable']

const VUL_LEVEL_OPTIONS = [
  { value: '', label: '全部' },
  { value: '严重', label: '严重' },
  { value: '高危', label: '高危' },
  { value: '中危', label: '中危' },
  { value: '低危', label: '低危' },
]

export default function ResultPages() {
  const { kind } = useParams<{ kind: string }>()
  const kindKey = kind && VALID_KINDS.includes(kind) ? kind : 'plugin'

  const [page, setPage] = useState(1)
  const [search, setSearch] = useState('')
  const [vulLevel, setVulLevel] = useState('')
  const [searchDraft, setSearchDraft] = useState('')
  const [detail, setDetail] = useState<Row | null>(null)

  // 切换结果类型时重置分页与筛选条件
  useEffect(() => {
    setPage(1)
    setSearch('')
    setVulLevel('')
    setSearchDraft('')
    setDetail(null)
  }, [kindKey])

  const config = useMemo<KindConfig>(() => {
    if (kindKey === 'plugin') {
      return {
        title: '插件结果',
        endpoint: 'plugin_list',
        detailFields: [
          ['id', 'ID'],
          ['plugin_name', '插件名称'],
          ['app_name', '应用名称'],
          ['status', '状态'],
          ['create_time', '创建时间'],
          ['content', '扫描内容'],
        ],
        columns: [
          idCol,
          {
            key: 'plugin_name',
            header: '插件名称',
            className: 'max-w-48 truncate',
            render: (row) => (
              <button
                type="button"
                onClick={() => setDetail(row)}
                className="max-w-full cursor-pointer truncate text-primary hover:underline"
              >
                {str(row.plugin_name ?? row.name)}
              </button>
            ),
          },
          { key: 'app_name', header: '应用名称' },
          {
            key: 'status',
            header: '状态',
            render: (row) => statusBadge(row.status),
          },
          {
            key: 'content',
            header: '扫描内容摘要',
            className: 'max-w-72 truncate',
            render: (row) => (
              <span className="block max-w-72 truncate" title={str(row.content)}>
                {str(row.content)}
              </span>
            ),
          },
          timeCol,
        ],
      }
    }
    return {
      title: '漏洞结果',
      endpoint: 'vulnerable_list',
      detailFields: [
        ['id', 'ID'],
        ['name', '漏洞名称'],
        ['vul_level', '危害等级'],
        ['product_field', '影响范围'],
        ['product_type', '产品类型'],
        ['product_cate', '产品分类'],
        ['create_time', '创建时间'],
        ['description', '漏洞描述'],
      ],
      columns: [
        idCol,
        {
          key: 'name',
          header: '漏洞名称',
          className: 'max-w-56 truncate',
          render: (row) => (
            <button
              type="button"
              onClick={() => setDetail(row)}
              className="max-w-full cursor-pointer truncate text-primary hover:underline"
            >
              {str(row.name)}
            </button>
          ),
        },
        {
          key: 'vul_level',
          header: '危害等级',
          render: (row) => <VulLevelBadge value={row.vul_level} />,
        },
        {
          key: 'product_field',
          header: '影响范围',
          className: 'max-w-56 truncate',
          render: (row) => (
            <span className="block max-w-56 truncate" title={str(row.product_field)}>
              {str(row.product_field)}
            </span>
          ),
        },
        { key: 'product_type', header: '产品类型' },
        { key: 'product_cate', header: '产品分类' },
        timeCol,
      ],
    }
  }, [kindKey])

  const applyFilter = () => {
    setSearch(searchDraft.trim())
    setPage(1)
  }

  const { data, isLoading, refetch } = useQuery({
    queryKey: ['results', kindKey, page, search, vulLevel],
    queryFn: () =>
      apiPage<Row>(`/result/${config.endpoint}`, {
        page,
        limit: 10,
        search,
        ...(kindKey === 'vulnerable' ? { vul_level: vulLevel } : {}),
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
        placeholder="搜索关键词"
        value={searchDraft}
        onChange={(e) => setSearchDraft(e.target.value)}
        onKeyDown={(e) => {
          if (e.key === 'Enter') applyFilter()
        }}
        className="w-56"
      />
      {kindKey === 'vulnerable' && (
        <Select
          value={vulLevel}
          onValueChange={(value) => {
            setVulLevel(value)
            setPage(1)
          }}
        >
          <SelectTrigger className="w-28">
            <SelectValue placeholder="危害等级" />
          </SelectTrigger>
          <SelectContent>
            {VUL_LEVEL_OPTIONS.map((opt) => (
              <SelectItem key={opt.value} value={opt.value}>
                {opt.label}
              </SelectItem>
            ))}
          </SelectContent>
        </Select>
      )}
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
        <h1 className="text-lg font-semibold">{config.title}</h1>
        <p className="text-xs text-muted-foreground">扫描结果 - {config.title}列表，点击名称查看详情</p>
      </div>
      <DataTable
        columns={config.columns}
        rows={data?.rows ?? []}
        loading={isLoading}
        total={data?.count}
        currentPage={data?.current_page ?? page}
        totalPage={data?.total_page ?? 1}
        onPageChange={setPage}
        rowKey={(row) => String(row.id)}
        toolbar={toolbar}
        emptyText="暂无结果数据"
      />
      <Dialog open={detail !== null} onOpenChange={(open) => !open && setDetail(null)}>
        <DialogContent className="max-w-lg">
          <DialogHeader>
            <DialogTitle>
              {kindKey === 'plugin'
                ? str(detail?.plugin_name ?? detail?.name ?? '')
                : str(detail?.name ?? '')}
            </DialogTitle>
            <DialogDescription>{config.title}详情</DialogDescription>
          </DialogHeader>
          <div className="max-h-[60vh] space-y-2 overflow-y-auto pr-1">
            {config.detailFields.map(([field, label]) => {
              const value = str(detail?.[field])
              return (
                <div key={field} className="flex gap-3 text-xs">
                  <span className="w-20 shrink-0 text-muted-foreground">{label}</span>
                  <span className="min-w-0 flex-1 break-all whitespace-pre-wrap">{value}</span>
                </div>
              )
            })}
          </div>
        </DialogContent>
      </Dialog>
    </div>
  )
}
