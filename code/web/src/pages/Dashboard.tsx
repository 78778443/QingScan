import { useEffect, useMemo, useRef, useState } from 'react'
import { useNavigate } from 'react-router-dom'
import { useQuery } from '@tanstack/react-query'
import * as echarts from 'echarts'
import { Shield } from 'lucide-react'
import { apiGet } from '@/lib/api'
import { cn } from '@/lib/utils'
import { Button } from '@/components/ui/button'
import { Card, CardContent, CardHeader, CardTitle, CardAction } from '@/components/ui/card'
import { Skeleton } from '@/components/ui/skeleton'

interface SubInfo {
  name: string
  value: number
  route: string
}

interface DashboardGroup {
  name: string
  value: number
  subInfo: SubInfo[]
}

interface TongjiItem {
  key: string
  data: { name: string; value: number }[]
  title: string
}

function barOption(data: { name: string; value: number }[], color: string): echarts.EChartsOption {
  return {
    tooltip: { trigger: 'axis', axisPointer: { type: 'shadow' } },
    grid: { left: 8, right: 16, top: 24, bottom: 8, containLabel: true },
    xAxis: { type: 'category', data: data.map((d) => d.name), axisLabel: { fontSize: 10 } },
    yAxis: { type: 'value', axisLabel: { fontSize: 10 } },
    series: [
      {
        type: 'bar',
        data: data.map((d) => d.value),
        barMaxWidth: 32,
        itemStyle: { color, borderRadius: [2, 2, 0, 0] },
      },
    ],
  }
}

function EChart({ option, className }: { option: echarts.EChartsOption; className?: string }) {
  const containerRef = useRef<HTMLDivElement>(null)
  const chartRef = useRef<echarts.ECharts | null>(null)

  useEffect(() => {
    const el = containerRef.current
    if (!el) return
    const chart = echarts.init(el)
    chartRef.current = chart
    const onResize = () => chart.resize()
    window.addEventListener('resize', onResize)
    return () => {
      window.removeEventListener('resize', onResize)
      chart.dispose()
      chartRef.current = null
    }
  }, [])

  useEffect(() => {
    chartRef.current?.setOption(option, true)
  }, [option])

  return <div ref={containerRef} className={cn('h-64 w-full', className)} />
}

function ChartCard({
  title,
  data,
  color = '#52525b',
}: {
  title: string
  data: { name: string; value: number }[] | undefined
  color?: string
}) {
  const option = useMemo(() => barOption(data ?? [], color), [data, color])
  return (
    <Card>
      <CardHeader>
        <CardTitle>{title}</CardTitle>
      </CardHeader>
      <CardContent>
        {data && data.length > 0 ? (
          <EChart option={option} />
        ) : (
          <div className="flex h-64 items-center justify-center text-xs text-muted-foreground">暂无数据</div>
        )}
      </CardContent>
    </Card>
  )
}

export default function Dashboard() {
  const navigate = useNavigate()
  const [activeKey, setActiveKey] = useState('portCount')

  const { data: groups, isLoading: groupsLoading, isError: groupsError, refetch: refetchGroups } = useQuery({
    queryKey: ['index-dashboard'],
    queryFn: () => apiGet<DashboardGroup[]>('/index/dashboard'),
  })

  const { data: tongji, isLoading: tongjiLoading, isError: tongjiError, refetch: refetchTongji } = useQuery({
    queryKey: ['index-tongji'],
    queryFn: () => apiGet<TongjiItem[]>('/index/tongji'),
  })

  const loading = groupsLoading || tongjiLoading
  const failed = groupsError || tongjiError

  // 图表列表（忽略 zanzhu）
  const chartList = useMemo(() => (tongji ?? []).filter((t) => t.key !== 'zanzhu'), [tongji])
  const activeChart = chartList.find((t) => t.key === activeKey) ?? chartList[0]
  const hostChart = tongji?.find((t) => t.key === 'hostCount')
  const serviceChart = tongji?.find((t) => t.key === 'serviceCount')

  const assetTotal = useMemo(() => (groups ?? []).reduce((sum, g) => sum + (g.value ?? 0), 0), [groups])
  const scanTotal = useMemo(
    () => (groups ?? []).reduce((sum, g) => sum + (g.subInfo ?? []).reduce((s, i) => s + (i.value ?? 0), 0), 0),
    [groups],
  )
  const activeOption = useMemo(
    () => (activeChart ? barOption(activeChart.data, '#52525b') : null),
    [activeChart],
  )

  if (failed) {
    return (
      <div className="flex h-full flex-col items-center justify-center gap-3">
        <p className="text-sm text-muted-foreground">数据加载失败</p>
        <Button
          variant="outline"
          onClick={() => {
            void refetchGroups()
            void refetchTongji()
          }}
        >
          重试
        </Button>
      </div>
    )
  }

  return (
    <div className="space-y-6">
      {/* 欢迎区 */}
      <section className="flex items-center justify-between rounded-lg bg-gradient-to-r from-primary to-primary/70 px-6 py-5 text-primary-foreground">
        <div className="flex items-center gap-4">
          <div className="flex size-11 items-center justify-center rounded-sm bg-primary-foreground/15">
            <Shield className="size-6" />
          </div>
          <div>
            <h2 className="text-base font-semibold">欢迎使用 QingScan</h2>
            <p className="mt-0.5 text-xs opacity-80">一站式安全扫描运营平台</p>
          </div>
        </div>
        <div className="flex items-center gap-8">
          {loading ? (
            <Skeleton className="h-10 w-40 bg-primary-foreground/20" />
          ) : (
            <>
              <div className="text-center">
                <p className="text-xl font-semibold">{assetTotal}</p>
                <p className="text-xs opacity-80">资产总数</p>
              </div>
              <div className="text-center">
                <p className="text-xl font-semibold">{scanTotal}</p>
                <p className="text-xs opacity-80">扫描记录</p>
              </div>
              <div className="text-center">
                <p className="text-xl font-semibold">4</p>
                <p className="text-xs opacity-80">扫描模块</p>
              </div>
            </>
          )}
        </div>
      </section>

      {/* 统计卡片 */}
      <section className="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
        {loading ? (
          Array.from({ length: 4 }).map((_, i) => (
            <Card key={i}>
              <CardContent className="space-y-3 pt-1">
                <Skeleton className="h-4 w-16" />
                <Skeleton className="h-8 w-20" />
                <Skeleton className="h-6 w-full" />
              </CardContent>
            </Card>
          ))
        ) : (
          (groups ?? []).map((g) => (
            <Card key={g.name}>
              <CardHeader>
                <CardTitle className="text-muted-foreground">{g.name}</CardTitle>
              </CardHeader>
              <CardContent className="space-y-3">
                <p className="text-2xl font-semibold tabular-nums">{g.value}</p>
                <div className="flex flex-wrap gap-1.5">
                  {(g.subInfo ?? []).map((s) => (
                    <Button key={s.name} variant="outline" size="xs" onClick={() => s.route && navigate(s.route)}>
                      {s.name} {s.value}
                    </Button>
                  ))}
                </div>
              </CardContent>
            </Card>
          ))
        )}
      </section>

      {/* 图表区 */}
      <section className="grid grid-cols-1 gap-4 lg:grid-cols-2">
        {/* 左侧：可切换图表 */}
        <Card>
          <CardHeader>
            <CardTitle>{activeChart?.title ?? '统计图表'}</CardTitle>
            <CardAction>
              <div className="flex flex-wrap gap-1">
                {chartList.map((t) => (
                  <Button
                    key={t.key}
                    size="xs"
                    variant={t.key === activeChart?.key ? 'default' : 'ghost'}
                    onClick={() => setActiveKey(t.key)}
                  >
                    {t.title}
                  </Button>
                ))}
              </div>
            </CardAction>
          </CardHeader>
          <CardContent>
            {loading ? (
              <Skeleton className="h-64 w-full" />
            ) : activeOption && activeChart && activeChart.data.length > 0 ? (
              <EChart option={activeOption} />
            ) : (
              <div className="flex h-64 items-center justify-center text-xs text-muted-foreground">暂无数据</div>
            )}
          </CardContent>
        </Card>

        {/* 右侧：主机统计 + 服务统计 */}
        <div className="space-y-4">
          <ChartCard title={hostChart?.title ?? '主机统计'} data={hostChart?.data} color="#71717a" />
          <ChartCard title={serviceChart?.title ?? '服务统计'} data={serviceChart?.data} color="#a1a1aa" />
        </div>
      </section>
    </div>
  )
}
