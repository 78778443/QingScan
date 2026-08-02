import { useEffect, useState } from 'react'
import { useParams } from 'react-router-dom'
import { useQuery } from '@tanstack/react-query'
import { apiPage } from '@/lib/api'
import { DataTable, severityColor, type Column } from '@/components/DataTable'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { Badge } from '@/components/ui/badge'
import { MagnifyingGlass, ArrowsClockwise } from '@phosphor-icons/react'

type Row = Record<string, unknown>

function str(v: unknown): string {
  if (v === undefined || v === null || v === '') return '-'
  if (typeof v === 'object') return JSON.stringify(v)
  return String(v)
}

function PortStatusBadge({ value }: { value: unknown }) {
  const raw = String(value ?? '').trim()
  if (raw === '0' || raw === '开放') {
    return <Badge className={severityColor('low')}>开放</Badge>
  }
  if (raw === '1' || raw === '关闭') {
    return <Badge variant="secondary">关闭</Badge>
  }
  return <Badge variant="outline">{raw === '' ? '-' : raw}</Badge>
}

// 子域名状态：1=存活(绿) / 0=失效(灰)
function SubdomainStatusBadge({ value }: { value: unknown }) {
  const raw = String(value ?? '').trim()
  if (raw === '1') return <Badge className={severityColor('low')}>存活</Badge>
  if (raw === '0') return <Badge variant="secondary">失效</Badge>
  return <Badge variant="outline">{raw === '' ? '-' : raw}</Badge>
}

function StatusCodeBadge({ value }: { value: unknown }) {
  const n = Number(value)
  if (Number.isNaN(n) || value === '' || value === undefined || value === null) {
    return <Badge variant="outline">{str(value)}</Badge>
  }
  if (n >= 400) {
    return <Badge className={severityColor('critical')}>{n}</Badge>
  }
  if (n >= 300) {
    return <Badge className={severityColor('medium')}>{n}</Badge>
  }
  if (n === 200) {
    return <Badge className={severityColor('low')}>{n}</Badge>
  }
  return <Badge variant="outline">{n}</Badge>
}

const timeCol: Column<Row> = {
  key: 'create_time',
  header: '创建时间',
  className: 'whitespace-nowrap',
}

const idCol: Column<Row> = {
  key: 'id',
  header: 'ID',
  className: 'w-16',
}

interface KindConfig {
  title: string
  endpoint: string
  columns: Column<Row>[]
}

const kindConfig: Record<string, KindConfig> = {
  host: {
    title: '主机资产',
    endpoint: 'host_list',
    columns: [
      idCol,
      { key: 'domain', header: '域名' },
      { key: 'host', header: '主机名' },
      { key: 'ip', header: 'IP 地址' },
      { key: 'isp', header: 'ISP' },
      { key: 'country', header: '国家' },
      { key: 'region', header: '地区' },
      { key: 'city', header: '城市' },
      timeCol,
    ],
  },
  port: {
    title: '端口资产',
    endpoint: 'port_list',
    columns: [
      idCol,
      { key: 'host', header: '主机' },
      { key: 'port', header: '端口' },
      { key: 'type', header: '类型' },
      { key: 'service', header: '服务' },
      {
        key: 'is_close',
        header: '状态',
        render: (row) => <PortStatusBadge value={row.is_close} />,
      },
      { key: 'os', header: '操作系统' },
      timeCol,
    ],
  },
  domain: {
    title: '域名资产',
    endpoint: 'domain_list',
    columns: [
      idCol,
      { key: 'domain', header: '域名' },
      timeCol,
    ],
  },
  subdomain: {
    title: '子域名资产',
    endpoint: 'subdomain_list',
    columns: [
      idCol,
      { key: 'subdomain', header: '子域名', className: 'max-w-64 truncate' },
      { key: 'ip', header: 'IP 地址' },
      { key: 'cname', header: 'CNAME', className: 'max-w-48 truncate' },
      { key: 'level', header: '级别' },
      {
        key: 'status',
        header: '状态',
        render: (row) => <SubdomainStatusBadge value={row.status} />,
      },
      timeCol,
    ],
  },
  url: {
    title: 'URL 资产',
    endpoint: 'url_list',
    columns: [
      idCol,
      { key: 'url', header: 'URL', className: 'max-w-96 truncate' },
      {
        key: 'status_code',
        header: '状态码',
        render: (row) => <StatusCodeBadge value={row.status_code} />,
      },
      { key: 'title', header: '标题', className: 'max-w-64 truncate' },
      timeCol,
    ],
  },
}

const VALID_KINDS = Object.keys(kindConfig)

export default function Assets() {
  const { kind } = useParams<{ kind: string }>()
  const kindKey = kind && VALID_KINDS.includes(kind) ? kind : 'host'
  const config = kindConfig[kindKey]

  const [page, setPage] = useState(1)
  const [keyword, setKeyword] = useState('')
  const [port, setPort] = useState('')
  const [service, setService] = useState('')
  const [keywordDraft, setKeywordDraft] = useState('')
  const [portDraft, setPortDraft] = useState('')
  const [serviceDraft, setServiceDraft] = useState('')

  // 切换资产类型时重置分页与筛选条件
  useEffect(() => {
    setPage(1)
    setKeyword('')
    setPort('')
    setService('')
    setKeywordDraft('')
    setPortDraft('')
    setServiceDraft('')
  }, [kindKey])

  const applyFilter = () => {
    setKeyword(keywordDraft.trim())
    setPort(portDraft.trim())
    setService(serviceDraft.trim())
    setPage(1)
  }

  const { data, isLoading, refetch } = useQuery({
    queryKey: ['assets', kindKey, page, keyword, port, service],
    queryFn: () =>
      apiPage<Row>(`/asm/${config.endpoint}`, {
        page,
        limit: 10,
        keyword,
        ...(kindKey === 'port' ? { port, service } : {}),
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
        value={keywordDraft}
        onChange={(e) => setKeywordDraft(e.target.value)}
        onKeyDown={(e) => {
          if (e.key === 'Enter') applyFilter()
        }}
        className="w-56"
      />
      {kindKey === 'port' && (
        <Input
          placeholder="端口"
          value={portDraft}
          onChange={(e) => setPortDraft(e.target.value)}
          onKeyDown={(e) => {
            if (e.key === 'Enter') applyFilter()
          }}
          className="w-24"
        />
      )}
      {kindKey === 'port' && (
        <Input
          placeholder="服务"
          value={serviceDraft}
          onChange={(e) => setServiceDraft(e.target.value)}
          onKeyDown={(e) => {
            if (e.key === 'Enter') applyFilter()
          }}
          className="w-32"
        />
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
        <p className="text-xs text-muted-foreground">资产管理 - {config.title}列表</p>
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
        emptyText="暂无资产数据"
      />
    </div>
  )
}
