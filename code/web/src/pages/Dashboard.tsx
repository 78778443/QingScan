import { useEffect, useMemo, useRef, useState } from 'react'
import { useNavigate } from 'react-router-dom'
import { useQuery } from '@tanstack/react-query'
import * as echarts from 'echarts'
import { Bug, Code2, Globe, Inbox, Server, ShieldCheck, type LucideIcon } from 'lucide-react'
import { apiGet } from '@/lib/api'
import { cn } from '@/lib/utils'
import { Button } from '@/components/ui/button'
import { Card, CardAction, CardContent, CardHeader, CardTitle } from '@/components/ui/card'
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

/** 统计卡图标块：蓝 / 青 / 紫 / 橙 */
const CARD_META: { icon: LucideIcon; gradient: string; shadow: string }[] = [
  { icon: Globe, gradient: 'from-blue-500 to-blue-600', shadow: 'shadow-blue-500/30' },
  { icon: Server, gradient: 'from-cyan-500 to-cyan-600', shadow: 'shadow-cyan-500/30' },
  { icon: Code2, gradient: 'from-violet-500 to-violet-600', shadow: 'shadow-violet-500/30' },
  { icon: Bug, gradient: 'from-orange-500 to-orange-600', shadow: 'shadow-orange-500/30' },
]

const ANIM = 'animate-in fade-in slide-in-from-bottom-2 duration-500'

/** 读取主题 CSS 变量（--chart-1..5），读不到用兜底色 */
function chartCssVar(name: string, fallback: string): string {
  if (typeof document === 'undefined') return fallback
  const value = getComputedStyle(document.documentElement).getPropertyValue(name).trim()
  return value || fallback
}

function barOption(
  data: { name: string; value: number }[],
  opts: { gradient?: [string, string]; color?: string },
): echarts.EChartsOption {
  const itemStyle = opts.gradient
    ? {
        borderRadius: [6, 6, 0, 0],
        color: new echarts.graphic.LinearGradient(0, 0, 0, 1, [
          { offset: 0, color: opts.gradient[0] },
          { offset: 1, color: opts.gradient[1] },
        ]),
      }
    : { borderRadius: [6, 6, 0, 0], color: opts.color ?? '#3b82f6' }
  return {
    tooltip: {
      trigger: 'axis',
      axisPointer: { type: 'shadow' },
      backgroundColor: 'rgba(255,255,255,0.95)',
      borderColor: '#e2e8f0',
      textStyle: { color: '#334155', fontSize: 12 },
    },
    grid: { left: 8, right: 16, top: 32, bottom: 8, containLabel: true },
    xAxis: {
      type: 'category',
      data: data.map((d) => d.name),
      axisLine: { lineStyle: { color: '#e2e8f0' } },
      axisTick: { show: false },
      axisLabel: { color: '#94a3b8', fontSize: 11 },
    },
    yAxis: {
      type: 'value',
      splitLine: { lineStyle: { color: '#f1f5f9' } },
      axisLabel: { color: '#94a3b8', fontSize: 11 },
    },
    series: [
      {
        type: 'bar',
        data: data.map((d) => d.value),
        barMaxWidth: 32,
        itemStyle,
        emphasis: { itemStyle: { opacity: 0.85 } },
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

  return <div ref={containerRef} className={cn('h-[300px] w-full', className)} />
}

function EmptyState({ className }: { className?: string }) {
  return (
    <div
      className={cn(
        'flex flex-col items-center justify-center gap-2 text-xs text-muted-foreground',
        className,
      )}
    >
      <Inbox className="size-5 opacity-60" />
      <span>暂无数据</span>
    </div>
  )
}

function MiniChart({
  title,
  data,
  color,
}: {
  title: string
  data: { name: string; value: number }[] | undefined
  color: string
}) {
  const option = useMemo(() => barOption(data ?? [], { color }), [data, color])
  return (
    <div>
      <p className="mb-1 text-xs font-medium text-muted-foreground">{title}</p>
      {data && data.length > 0 ? (
        <EChart option={option} className="h-[130px] w-full" />
      ) : (
        <EmptyState className="h-[130px]" />
      )}
    </div>
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

  // 主题色（CSS 变量，读不到兜底 #3b82f6）
  const chartColors = useMemo(
    () => ({
      c1: chartCssVar('--chart-1', '#3b82f6'),
      c2: chartCssVar('--chart-2', '#3b82f6'),
      c3: chartCssVar('--chart-3', '#3b82f6'),
      c4: chartCssVar('--chart-4', '#3b82f6'),
      c5: chartCssVar('--chart-5', '#3b82f6'),
    }),
    [],
  )

  const activeOption = useMemo(
    () =>
      activeChart && activeChart.data.length > 0
        ? barOption(activeChart.data, { gradient: ['#60a5fa', '#3b82f6'] })
        : null,
    [activeChart],
  )

  const welcomeStats = useMemo(
    () => [
      { label: '资产总数', value: assetTotal },
      { label: '扫描记录', value: scanTotal },
      { label: '扫描模块', value: 4 },
    ],
    [assetTotal, scanTotal],
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
      {/* 欢迎卡 */}
      <section
        className={cn(
          'relative overflow-hidden rounded-2xl bg-gradient-to-r from-primary via-blue-500 to-violet-500 px-6 py-6 text-white shadow-lg shadow-primary/20',
          ANIM,
        )}
      >
        {/* 装饰圆 */}
        <div className="pointer-events-none absolute -right-20 -top-20 size-72 rounded-full bg-white/10 blur-3xl" />
        <div className="pointer-events-none absolute -bottom-24 right-40 size-48 rounded-full bg-violet-300/20 blur-2xl" />
        {/* 水印图标 */}
        <ShieldCheck className="pointer-events-none absolute -right-4 -top-4 size-52 rotate-12 opacity-20" />
        <div className="relative z-10 flex flex-col gap-5 md:flex-row md:items-center md:justify-between">
          <div>
            <h2 className="text-2xl font-bold">欢迎使用 QingScan</h2>
            <p className="mt-1 text-sm opacity-85">一站式安全扫描运营平台，资产 · 扫描 · 报告尽在掌握</p>
          </div>
          <div className="flex items-center">
            {loading ? (
              <Skeleton className="h-14 w-64 bg-white/20" />
            ) : (
              welcomeStats.map((s, i) => (
                <div
                  key={s.label}
                  className={cn('min-w-24 text-center', i > 0 && 'border-l border-white/25 pl-6 md:pl-8')}
                >
                  <p className="text-3xl font-bold tabular-nums">{s.value}</p>
                  <p className="mt-0.5 text-xs opacity-80">{s.label}</p>
                </div>
              ))
            )}
          </div>
        </div>
      </section>

      {/* 统计卡片 */}
      <section className="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
        {loading
          ? Array.from({ length: 4 }).map((_, i) => (
              <Card
                key={i}
                className="rounded-xl border shadow-sm animate-in fade-in slide-in-from-bottom-2 duration-500"
                style={{ animationDelay: `${i * 60}ms` }}
              >
                <CardContent className="space-y-3 pt-4">
                  <Skeleton className="h-11 w-11 rounded-xl" />
                  <Skeleton className="h-8 w-20" />
                  <Skeleton className="h-6 w-full" />
                </CardContent>
              </Card>
            ))
          : (groups ?? []).map((g, i) => {
              const meta = CARD_META[i % CARD_META.length]
              const Icon = meta.icon
              return (
                <Card
                  key={g.name}
                  className="group rounded-xl border shadow-sm transition-all duration-300 hover:-translate-y-0.5 hover:shadow-md animate-in fade-in slide-in-from-bottom-2 duration-500"
                  style={{ animationDelay: `${i * 60}ms` }}
                >
                  <CardContent className="flex flex-col gap-3 pt-4">
                    <div className="flex items-start justify-between">
                      <div
                        className={cn(
                          'flex size-11 items-center justify-center rounded-xl bg-gradient-to-br text-white shadow-lg transition-transform duration-300 group-hover:scale-105',
                          meta.gradient,
                          meta.shadow,
                        )}
                      >
                        <Icon className="size-5" />
                      </div>
                      <span className="text-sm text-muted-foreground">{g.name}</span>
                    </div>
                    <p className="text-3xl font-bold tabular-nums tracking-tight">{g.value}</p>
                    <div className="flex flex-wrap gap-1.5">
                      {(g.subInfo ?? []).map((s) => (
                        <Button
                          key={s.name}
                          variant="ghost"
                          size="xs"
                          className="h-7 rounded-full bg-muted/50 px-2.5 font-normal text-muted-foreground hover:bg-accent hover:text-foreground"
                          onClick={() => s.route && navigate(s.route)}
                        >
                          <span className={cn('size-1.5 shrink-0 rounded-full bg-gradient-to-r', meta.gradient)} />
                          {s.name}
                          <span className="font-medium tabular-nums text-foreground">{s.value}</span>
                        </Button>
                      ))}
                    </div>
                  </CardContent>
                </Card>
              )
            })}
      </section>

      {/* 图表区 */}
      <section className="grid grid-cols-1 gap-4 lg:grid-cols-2">
        {/* 左侧：可切换图表 */}
        <Card className={cn('rounded-xl border shadow-sm', ANIM)}>
          <CardHeader>
            <CardTitle>{activeChart?.title ?? '统计图表'}</CardTitle>
            <CardAction>
              <div className="flex flex-wrap gap-1">
                {chartList.map((t) => (
                  <Button
                    key={t.key}
                    size="xs"
                    variant={t.key === activeChart?.key ? 'default' : 'ghost'}
                    className="rounded-full px-2.5"
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
              <Skeleton className="h-[300px] w-full" />
            ) : activeOption ? (
              <EChart option={activeOption} />
            ) : (
              <EmptyState className="h-[300px]" />
            )}
          </CardContent>
        </Card>

        {/* 右侧：主机统计 + 服务统计 */}
        <Card
          className="rounded-xl border shadow-sm animate-in fade-in slide-in-from-bottom-2 duration-500"
          style={{ animationDelay: '80ms' }}
        >
          <CardHeader>
            <CardTitle>主机与服务统计</CardTitle>
          </CardHeader>
          <CardContent className="space-y-5">
            {loading ? (
              <>
                <Skeleton className="h-[150px] w-full" />
                <Skeleton className="h-[150px] w-full" />
              </>
            ) : (
              <>
                <MiniChart
                  title={hostChart?.title ?? '主机统计'}
                  data={hostChart?.data}
                  color={chartColors.c1}
                />
                <MiniChart
                  title={serviceChart?.title ?? '服务统计'}
                  data={serviceChart?.data}
                  color={chartColors.c2}
                />
              </>
            )}
          </CardContent>
        </Card>
      </section>
    </div>
  )
}
