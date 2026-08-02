import { useEffect, useMemo, useRef, useState } from 'react'
import { useNavigate } from 'react-router-dom'
import { useQuery } from '@tanstack/react-query'
import * as echarts from 'echarts'
import { Bug, Code2, ExternalLink, Globe, Inbox, Server, ShieldCheck, type LucideIcon } from 'lucide-react'
import { apiGet } from '@/lib/api'
import { cn } from '@/lib/utils'
import { Button } from '@/components/ui/button'
import { Card, CardContent } from '@/components/ui/card'
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

interface DataPoint {
  name: string
  value: number
}

interface TongjiItem {
  key: string
  data: DataPoint[]
  title: string
}

type ChartType = 'bar' | 'hbar' | 'pie' | 'line' | 'gauge'

interface ChartSpec {
  key: string
  type: ChartType
  group: string
  href?: string
  /** CHART_COLORS 下标（非自定义配色的图表使用） */
  color?: number
  /** 柱状图是否使用渐变 */
  gradient?: boolean
}

/** 统计卡图标块：蓝 / 青 / 紫 / 橙 */
const CARD_META: { icon: LucideIcon; gradient: string; shadow: string }[] = [
  { icon: Globe, gradient: 'from-blue-500 to-blue-600', shadow: 'shadow-blue-500/30' },
  { icon: Server, gradient: 'from-cyan-500 to-cyan-600', shadow: 'shadow-cyan-500/30' },
  { icon: Code2, gradient: 'from-violet-500 to-violet-600', shadow: 'shadow-violet-500/30' },
  { icon: Bug, gradient: 'from-orange-500 to-orange-600', shadow: 'shadow-orange-500/30' },
]

const ANIM = 'animate-in fade-in slide-in-from-bottom-2 duration-500'

/** 空数据时的默认示例数据（标注"示例"避免误读为真实数据） */
const DEFAULT_DATA: Record<string, { name: string; value: number }[]> = {
  vuln_severity: [
    { name: 'low', value: 12 }, { name: 'medium', value: 8 },
    { name: 'high', value: 5 }, { name: 'critical', value: 2 },
  ],
  vuln_source: [
    { name: 'Web漏洞检测', value: 15 }, { name: '通用漏洞扫描', value: 8 }, { name: '漏洞验证', value: 3 },
  ],
  vuln_trend: Array.from({ length: 14 }, (_, i) => ({
    name: `${String((new Date().getMonth() + 1) % 12 + 1).padStart(2, '0')}-${String(i + 1).padStart(2, '0')}`,
    value: 2 + Math.round(i * 0.8),
  })),
  asset_overview: [
    { name: '主机', value: 24 }, { name: '端口', value: 86 }, { name: '域名', value: 32 },
    { name: '子域名', value: 128 }, { name: 'URL', value: 256 },
  ],
  port_top: [
    { name: '80', value: 32 }, { name: '443', value: 28 }, { name: '22', value: 18 },
    { name: '3306', value: 12 }, { name: '6379', value: 8 }, { name: '8080', value: 6 },
  ],
  service_dist: [
    { name: 'http', value: 40 }, { name: 'https', value: 28 }, { name: 'ssh', value: 18 },
    { name: 'mysql', value: 12 }, { name: 'redis', value: 8 },
  ],
  workorder_status: [
    { name: '待派发', value: 3 }, { name: '已派发', value: 5 }, { name: '已确认', value: 4 },
    { name: '修复待确认', value: 2 }, { name: '已修复', value: 8 },
  ],
  workorder_type: [
    { name: '漏洞', value: 12 }, { name: '系统', value: 5 }, { name: '其他', value: 2 },
  ],
  workorder_trend: Array.from({ length: 14 }, (_, i) => ({
    name: `${String((new Date().getMonth() + 1) % 12 + 1).padStart(2, '0')}-${String(i + 1).padStart(2, '0')}`,
    value: Math.max(0, 4 - Math.round(i * 0.3)),
  })),
  audit_rules: [
    { name: 'php.lang.security.eval', value: 9 }, { name: 'php.lang.security.sql-concat', value: 7 },
    { name: 'php.lang.taint.command-injection', value: 5 }, { name: 'php.lang.security.xss', value: 4 },
    { name: 'php.lang.taint.ssrf', value: 3 },
  ],
  audit_severity: [
    { name: 'error', value: 18 }, { name: 'warning', value: 32 },
  ],
  audit_files: [
    { name: 'index.php', value: 8 }, { name: 'app.php', value: 6 }, { name: 'api/user.php', value: 5 },
    { name: 'admin/login.php', value: 4 }, { name: 'config/db.php', value: 3 },
  ],
}

/** 14 个图表注册表（顺序即展示顺序） */
const CHART_SPECS: ChartSpec[] = [
  // 漏洞扫描
  { key: 'vuln_severity', type: 'pie', group: '漏洞扫描', href: '/webscan/web-vuln' },
  { key: 'vuln_source', type: 'bar', group: '漏洞扫描', href: '/webscan/web-vuln', color: 0, gradient: true },
  { key: 'vuln_trend', type: 'line', group: '漏洞扫描', href: '/webscan/web-vuln', color: 0 },
  // 资产清点
  { key: 'asset_overview', type: 'bar', group: '资产清点', href: '/asm/host', color: 0, gradient: true },
  { key: 'port_top', type: 'hbar', group: '资产清点', href: '/asm/port', color: 2 },
  { key: 'service_dist', type: 'pie', group: '资产清点', href: '/asm/port' },
  // 工单管理
  { key: 'workorder_status', type: 'pie', group: '工单管理', href: '/workorder' },
  { key: 'workorder_type', type: 'bar', group: '工单管理', href: '/workorder', color: 2 },
  { key: 'workorder_trend', type: 'line', group: '工单管理', href: '/workorder', color: 2 },
  // 代码审计
  { key: 'audit_rules', type: 'hbar', group: '代码审计', href: '/code', color: 1 },
  { key: 'audit_severity', type: 'pie', group: '代码审计', href: '/code' },
  { key: 'audit_files', type: 'hbar', group: '代码审计', href: '/code', color: 4 },
]

const CHART_GROUPS = ['漏洞扫描', '资产清点', '工单管理', '代码审计']

const AXIS_LABEL = { color: '#94a3b8', fontSize: 11 }
const SPLIT_LINE = { lineStyle: { color: '#f1f5f9' } }
const AXIS_LINE = { lineStyle: { color: '#e2e8f0' } }
const TOOLTIP_AXIS = {
  trigger: 'axis',
  axisPointer: { type: 'shadow' },
  backgroundColor: 'rgba(255,255,255,0.95)',
  borderColor: '#e2e8f0',
  textStyle: { color: '#334155', fontSize: 12 },
}
const TOOLTIP_ITEM = {
  trigger: 'item',
  backgroundColor: 'rgba(255,255,255,0.95)',
  borderColor: '#e2e8f0',
  textStyle: { color: '#334155', fontSize: 12 },
}

/** oklch(...) → #rrggbb(aa)，zrender 不识别 oklch，须转为 hex */
function oklchToHex(raw: string): string | null {
  const m = raw.match(/^oklch\(\s*([\d.]+%?)\s+([\d.]+%?)\s+([\d.]+)(?:\s*\/\s*([\d.]+%?))?\s*\)$/i)
  if (!m) return null
  const L = m[1].endsWith('%') ? parseFloat(m[1]) / 100 : parseFloat(m[1])
  let C = parseFloat(m[2])
  if (m[2].endsWith('%')) C = (C / 100) * 0.4
  const H = parseFloat(m[3])
  const alpha = m[4] ? (m[4].endsWith('%') ? parseFloat(m[4]) / 100 : parseFloat(m[4])) : 1
  // oklch → oklab → linear sRGB → sRGB
  const a = C * Math.cos((H * Math.PI) / 180)
  const b = C * Math.sin((H * Math.PI) / 180)
  const labL = L + 0.3963377774 * a + 0.2158037573 * b
  const labM = L - 0.1055613458 * a - 0.0638541728 * b
  const labS = L - 0.0894841775 * a - 1.291485548 * b
  const linL = labL ** 3
  const linM = labM ** 3
  const linS = labS ** 3
  const toSrgb = (c: number) => {
    const v = c <= 0.0031308 ? 12.92 * c : 1.055 * Math.pow(c, 1 / 2.4) - 0.055
    return Math.round(Math.min(1, Math.max(0, v)) * 255)
      .toString(16)
      .padStart(2, '0')
  }
  const rgb =
    toSrgb(4.0767416621 * linL - 3.3077115913 * linM + 0.2309699292 * linS) +
    toSrgb(-1.2684380046 * linL + 2.6097574011 * linM - 0.3413193965 * linS) +
    toSrgb(-0.0041960863 * linL - 0.7034186147 * linM + 1.707614701 * linS)
  if (alpha >= 1) return `#${rgb}`
  const aHex = Math.round(Math.min(1, Math.max(0, alpha)) * 255)
    .toString(16)
    .padStart(2, '0')
  return `#${rgb}${aHex}`
}

/** 读取主题 CSS 变量（--chart-1..5），转成 echarts 可用的颜色 */
function cssVar(name: string, fallback: string): string {
  if (typeof document === 'undefined') return fallback
  const raw = getComputedStyle(document.documentElement).getPropertyValue(name).trim()
  if (!raw) return fallback
  return oklchToHex(raw) ?? raw
}

/** 主题色板：监听 .dark 类变化，深色模式切换时重新读取 CSS 变量 */
function useChartColors(): string[] {
  const [tick, setTick] = useState(0)
  useEffect(() => {
    const el = document.documentElement
    const observer = new MutationObserver(() => setTick((n) => n + 1))
    observer.observe(el, { attributes: true, attributeFilter: ['class'] })
    return () => observer.disconnect()
  }, [])
  return useMemo(
    () => [
      cssVar('--chart-1', '#3b82f6'),
      cssVar('--chart-2', '#10b981'),
      cssVar('--chart-3', '#8b5cf6'),
      cssVar('--chart-4', '#f59e0b'),
      cssVar('--chart-5', '#ef4444'),
    ],
    // eslint-disable-next-line react-hooks/exhaustive-deps
    [tick],
  )
}

/** 漏洞严重级别配色：low=绿 medium=黄 high=橙 critical=红 */
function severityColor(name: string, colors: string[]): string {
  const n = name.toLowerCase()
  if (n.includes('low') || n.includes('低')) return colors[1]
  if (n.includes('medium') || n.includes('中')) return colors[3]
  if (n.includes('critical') || n.includes('严重') || n.includes('致命')) return '#dc2626'
  if (n.includes('high') || n.includes('高')) return colors[4]
  return colors[0]
}

/** 审计级别配色：error=红 warning=黄 */
function auditSeverityColor(name: string, colors: string[]): string {
  const n = name.toLowerCase()
  if (n.includes('error') || n.includes('错误') || n.includes('危险')) return colors[4]
  if (n.includes('warn') || n.includes('警告')) return colors[3]
  return colors[2]
}

function gradientOf(color: string): echarts.graphic.LinearGradient {
  return new echarts.graphic.LinearGradient(0, 0, 0, 1, [
    { offset: 0, color },
    { offset: 1, color: `${color}44` },
  ])
}

function barOption(data: DataPoint[], color: string, gradient = false): any {
  return {
    tooltip: TOOLTIP_AXIS,
    grid: { left: 8, right: 16, top: 32, bottom: 8, containLabel: true },
    xAxis: {
      type: 'category',
      data: data.map((d) => d.name),
      axisLine: AXIS_LINE,
      axisTick: { show: false },
      axisLabel: AXIS_LABEL,
    },
    yAxis: { type: 'value', splitLine: SPLIT_LINE, axisLabel: AXIS_LABEL },
    series: [
      {
        type: 'bar',
        data: data.map((d) => d.value),
        barMaxWidth: 32,
        itemStyle: {
          borderRadius: [4, 4, 0, 0],
          color: gradient ? gradientOf(color) : color,
        },
        emphasis: { itemStyle: { opacity: 0.85 } },
      },
    ],
  }
}

function hbarOption(data: DataPoint[], color: string): any {
  return {
    tooltip: TOOLTIP_AXIS,
    grid: { left: 8, right: 16, top: 8, bottom: 8, containLabel: true },
    xAxis: { type: 'value', splitLine: SPLIT_LINE, axisLabel: AXIS_LABEL },
    yAxis: {
      type: 'category',
      inverse: true,
      data: data.map((d) => d.name),
      axisLine: { show: false },
      axisTick: { show: false },
      axisLabel: AXIS_LABEL,
    },
    series: [
      {
        type: 'bar',
        data: data.map((d) => d.value),
        barMaxWidth: 14,
        itemStyle: { borderRadius: [0, 4, 4, 0], color },
      },
    ],
  }
}

function lineOption(data: DataPoint[], color: string): any {
  return {
    tooltip: TOOLTIP_AXIS,
    grid: { left: 8, right: 16, top: 32, bottom: 8, containLabel: true },
    xAxis: {
      type: 'category',
      boundaryGap: false,
      data: data.map((d) => d.name),
      axisLine: AXIS_LINE,
      axisTick: { show: false },
      axisLabel: AXIS_LABEL,
    },
    yAxis: { type: 'value', splitLine: SPLIT_LINE, axisLabel: AXIS_LABEL },
    series: [
      {
        type: 'line',
        smooth: true,
        data: data.map((d) => d.value),
        symbol: 'circle',
        symbolSize: 6,
        lineStyle: { width: 2, color },
        itemStyle: { color },
        areaStyle: {
          color: new echarts.graphic.LinearGradient(0, 0, 0, 1, [
            { offset: 0, color: `${color}66` },
            { offset: 1, color: `${color}00` },
          ]),
        },
      },
    ],
  }
}

function pieOption(
  data: DataPoint[],
  colors: string[],
  colorFor?: (name: string, colors: string[]) => string,
): any {
  const colorOf = (name: string, i: number) =>
    colorFor ? colorFor(name, colors) : colors[i % colors.length]
  return {
    tooltip: { ...TOOLTIP_ITEM, formatter: '{b}: {c} ({d}%)' },
    legend: {
      bottom: 0,
      icon: 'circle',
      itemWidth: 8,
      itemHeight: 8,
      textStyle: { color: '#94a3b8', fontSize: 11 },
    },
    series: [
      {
        type: 'pie',
        radius: ['45%', '70%'],
        center: ['50%', '44%'],
        avoidLabelOverlap: true,
        itemStyle: { borderRadius: 4, borderWidth: 2, borderColor: 'transparent' },
        label: { show: true, formatter: '{b}: {d}%', color: '#94a3b8', fontSize: 11 },
        labelLine: { length: 8, length2: 8, lineStyle: { color: '#94a3b8' } },
        data: data.map((d, i) => ({
          name: d.name,
          value: d.value,
          itemStyle: { color: colorOf(d.name, i) },
        })),
      },
    ],
  }
}

function gaugeOption(data: DataPoint[]): any {
  const first = data[0]
  return {
    series: [
      {
        type: 'gauge',
        min: 0,
        max: Math.max(100, ...data.map((d) => d.value)),
        progress: { show: true, width: 8 },
        axisLine: { lineStyle: { width: 8 } },
        axisTick: { show: false },
        splitLine: { length: 6 },
        axisLabel: { fontSize: 9, color: '#94a3b8' },
        detail: { fontSize: 14, color: '#334155' },
        data: first ? [{ value: first.value, name: first.name }] : [],
      },
    ],
  }
}

function buildOption(
  type: ChartType,
  data: DataPoint[],
  colors: string[],
  colorFor?: (name: string, colors: string[]) => string,
  gradient = false,
): any {
  switch (type) {
    case 'hbar':
      return hbarOption(data, colors[0])
    case 'pie':
      return pieOption(data, colors, colorFor)
    case 'line':
      return lineOption(data, colors[0])
    case 'gauge':
      return gaugeOption(data)
    case 'bar':
    default:
      return barOption(data, colors[0], gradient)
  }
}

function EChart({ option, className }: { option: any; className?: string }) {
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

  return <div ref={containerRef} className={cn('h-[240px] w-full', className)} />
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

interface ChartCardProps {
  title: string
  data: DataPoint[] | undefined
  /** 展示默认示例数据的 key（真实数据为空时） */
  demoKey?: string
  type: ChartType
  href?: string
  /** CHART_COLORS 下标，未指定时用第一个 */
  color?: number
  /** 柱状图是否使用渐变 */
  gradient?: boolean
  /** 饼图自定义配色（如漏洞严重级别） */
  colorFor?: (name: string, colors: string[]) => string
  colors: string[]
  loading: boolean
  /** 入场动画延迟（ms） */
  delay?: number
}

function ChartCard({
  title,
  data,
  type,
  href,
  color = 0,
  gradient = false,
  colorFor,
  colors,
  loading,
  delay = 0,
  demoKey,
}: ChartCardProps) {
  const navigate = useNavigate()
  // 空数据判定：无数据或全部为 0（趋势/分布类接口会补 0 值条目）
  const isEmpty =
    !loading &&
    (data === undefined || data.length === 0 || data.every((d) => Number(d.value) === 0))
  const showDemo = isEmpty && !!demoKey
  const points = useMemo(() => {
    const source = showDemo ? (DEFAULT_DATA[demoKey!] ?? []) : (data ?? [])
    return source.map((d) => ({ name: d.name, value: Number(d.value) || 0 }))
  }, [data, showDemo, demoKey])
  const option = useMemo(
    () => buildOption(type, points, [colors[color], ...colors.filter((_, i) => i !== color)], colorFor, gradient),
    [type, points, colors, color, colorFor, gradient],
  )
  return (
    <div
      className={cn(
        'flex flex-col gap-3 rounded-lg border bg-card p-4 shadow-sm transition-all hover:-translate-y-0.5 hover:shadow-md animate-in fade-in slide-in-from-bottom-2 duration-500',
        href && 'cursor-pointer',
      )}
      style={delay > 0 ? { animationDelay: `${delay}ms` } : undefined}
      onClick={() => href && navigate(href)}
      role={href ? 'button' : undefined}
      title={href ? '点击查看详情' : undefined}
    >
      <div className="flex items-center justify-between gap-2">
        <p className="truncate text-sm font-medium">
          {title}
          {showDemo && (
            <span
              title="暂无真实数据，展示示例数据供预览"
              className="ml-1.5 rounded-sm bg-muted/60 px-1.5 py-px text-[10px] font-normal text-muted-foreground/70"
            >
              空数据示例
            </span>
          )}
        </p>
        {href && (
          <ExternalLink className="size-3.5 shrink-0 text-muted-foreground transition-colors group-hover:text-foreground" />
        )}
      </div>
      {loading ? (
        <Skeleton className="h-[240px] w-full" />
      ) : points.length > 0 || showDemo ? (
        <EChart option={option} className={showDemo ? 'opacity-45' : undefined} />
      ) : (
        <EmptyState className="h-[240px]" />
      )}
    </div>
  )
}

export default function Dashboard() {
  const navigate = useNavigate()
  const colors = useChartColors()

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

  // 按 key 索引统计接口数据，图表区按注册表取用（未知 key 自动忽略）
  const tongjiMap = useMemo(() => {
    const map = new Map<string, TongjiItem>()
    for (const t of tongji ?? []) {
      if (t && t.key) map.set(t.key, t)
    }
    return map
  }, [tongji])

  const assetTotal = useMemo(() => (groups ?? []).reduce((sum, g) => sum + (g.value ?? 0), 0), [groups])
  const scanTotal = useMemo(
    () => (groups ?? []).reduce((sum, g) => sum + (g.subInfo ?? []).reduce((s, i) => s + (i.value ?? 0), 0), 0),
    [groups],
  )

  const welcomeStats = useMemo(
    () => [
      { label: '资产总数', value: assetTotal },
      { label: '扫描记录', value: scanTotal },
      { label: '核心模块', value: 4 },
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
            <h2 className="text-2xl font-bold">QingScan 安全运营平台</h2>
            <p className="mt-1 text-sm opacity-85">
              漏洞扫描 · 代码审计 · 资产清点 · 工单推进，一站式安全运营
            </p>
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

      {/* 图表区：14 个图表按维度分组，3 列网格 */}
      <section className="space-y-6">
        {CHART_GROUPS.map((group) => (
          <div key={group} className="space-y-3">
            <h3 className="text-xs font-medium text-muted-foreground">{group}</h3>
            <div className="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-3">
              {CHART_SPECS.filter((spec) => spec.group === group).map((spec, i) => {
                const item = tongjiMap.get(spec.key)
                return (
                  <ChartCard
                    key={spec.key}
                    title={item?.title ?? spec.key}
                    data={item?.data}
                    demoKey={spec.key}
                    type={spec.type}
                    href={spec.href}
                    color={spec.color}
                    gradient={spec.gradient}
                    colors={colors}
                    colorFor={
                      spec.key === 'vuln_severity'
                        ? severityColor
                        : spec.key === 'audit_severity'
                          ? auditSeverityColor
                          : undefined
                    }
                    loading={tongjiLoading}
                    delay={i * 50}
                  />
                )
              })}
            </div>
          </div>
        ))}
      </section>
    </div>
  )
}
