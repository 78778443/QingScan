import { useEffect } from 'react'
import { NavLink, Outlet, useLocation, useNavigate } from 'react-router-dom'
import { useQuery } from '@tanstack/react-query'
import { toast } from 'sonner'
import {
  LayoutDashboard,
  Globe,
  Radar,
  Shield,
  ShieldCheck,
  Database,
  Bug,
  FolderSearch,
  Server,
  Network,
  Layers,
  Link,
  Code2,
  Ticket,
  Settings,
  LogOut,
  RefreshCw,
  type LucideIcon,
} from 'lucide-react'
import { apiGet, apiPost } from '@/lib/api'
import { cn } from '@/lib/utils'
import { Button } from '@/components/ui/button'

interface UserInfo {
  username?: string
  nickname?: string
  role?: string
}

interface MenuItem {
  label: string
  path: string
  icon?: LucideIcon
}

interface MenuGroup {
  title?: string
  items: MenuItem[]
}

const MENU: MenuGroup[] = [
  { items: [{ label: '主页', path: '/', icon: LayoutDashboard }] },
  { items: [{ label: '网站扫描', path: '/webscan/targets', icon: Globe }] },
  {
    title: '扫描结果',
    items: [
      { label: 'Web漏洞检测', path: '/webscan/web-vuln', icon: Radar },
      { label: '通用漏洞扫描', path: '/webscan/gen-vuln', icon: Shield },
      { label: 'SQL注入检测', path: '/webscan/sql-inject', icon: Database },
      { label: '漏洞验证', path: '/webscan/vul-verify', icon: Bug },
      { label: '目录扫描', path: '/webscan/dir-scan', icon: FolderSearch },
      { label: '指纹识别', path: '/webscan/finger', icon: Globe },
    ],
  },
  {
    title: '资产管理',
    items: [
      { label: '主机资产', path: '/asm/host', icon: Server },
      { label: '端口资产', path: '/asm/port', icon: Network },
      { label: '域名资产', path: '/asm/domain', icon: Globe },
      { label: '子域名', path: '/asm/subdomain', icon: Layers },
      { label: 'URL资产', path: '/asm/url', icon: Link },
    ],
  },
  {
    title: '安全运营',
    items: [
      { label: '代码审计', path: '/code', icon: Code2 },
      { label: '工单管理', path: '/workorder', icon: Ticket },
    ],
  },
]

const TITLES: Record<string, string> = {
  '/': '控制台',
  '/webscan/targets': '网站扫描',
  '/webscan/web-vuln': 'Web漏洞检测',
  '/webscan/gen-vuln': '通用漏洞扫描',
  '/webscan/sql-inject': 'SQL注入检测',
  '/webscan/vul-verify': '漏洞验证',
  '/webscan/dir-scan': '目录扫描',
  '/webscan/finger': '指纹识别',
  '/asm/host': '主机资产',
  '/asm/port': '端口资产',
  '/asm/domain': '域名资产',
  '/asm/subdomain': '子域名',
  '/asm/url': 'URL资产',
  '/code': '代码审计',
  '/workorder': '工单管理',
}

function pageTitle(pathname: string): string {
  if (TITLES[pathname]) return TITLES[pathname]
  if (pathname.startsWith('/webscan/')) return '扫描结果'
  if (pathname.startsWith('/asm/')) return '资产管理'
  if (pathname.startsWith('/result/')) return '漏洞结果'
  if (pathname.startsWith('/task')) return '扫描任务'
  if (pathname.startsWith('/code')) return '代码审计'
  if (pathname.startsWith('/workorder')) return '工单管理'
  return 'QingScan'
}

export default function AppLayout() {
  const navigate = useNavigate()
  const { pathname } = useLocation()

  const { data: user, isError } = useQuery({
    queryKey: ['auth-info'],
    queryFn: () => apiGet<UserInfo>('/auth/info'),
    retry: false,
  })

  useEffect(() => {
    if (isError) navigate('/login', { replace: true })
  }, [isError, navigate])

  const handleLogout = async () => {
    try {
      await apiPost('/auth/logout')
    } catch (err) {
      toast.error(err instanceof Error ? err.message : '退出失败')
    }
    window.location.href = '/login'
  }

  return (
    <div className="flex h-screen overflow-hidden">
      {/* 侧边栏 */}
      <aside className="flex w-60 shrink-0 flex-col border-r bg-sidebar text-sidebar-foreground">
        <div className="flex h-14 shrink-0 items-center gap-2.5 border-b px-4">
          <div className="flex size-8 shrink-0 items-center justify-center rounded-lg bg-gradient-to-br from-primary to-primary/60 shadow-sm">
            <ShieldCheck className="size-5 text-primary-foreground" />
          </div>
          <div className="min-w-0 leading-tight">
            <p className="text-sm font-bold tracking-tight">QingScan</p>
            <p className="text-[10px] text-muted-foreground">安全运营平台</p>
          </div>
        </div>

        <nav className="flex-1 overflow-y-auto px-3 py-3">
          {MENU.map((group, gi) => (
            <div key={gi} className="mb-4">
              {group.title && (
                <p className="mb-1 px-2 text-[11px] font-medium tracking-wider text-muted-foreground uppercase">
                  {group.title}
                </p>
              )}
              <ul className="space-y-0.5">
                {group.items.map((item) => {
                  const Icon = item.icon
                  return (
                    <li key={item.path}>
                      <NavLink
                        to={item.path}
                        end={item.path === '/'}
                        className={({ isActive }) =>
                          cn(
                            'flex items-center gap-2 rounded-md px-2 py-1.5 text-[13px] transition-colors',
                            'hover:bg-accent hover:text-accent-foreground',
                            isActive && 'bg-primary/10 font-medium text-primary hover:bg-primary/10 hover:text-primary',
                          )
                        }
                      >
                        {Icon && <Icon className="size-4 shrink-0" />}
                        <span className="truncate">{item.label}</span>
                      </NavLink>
                    </li>
                  )
                })}
              </ul>
            </div>
          ))}
        </nav>

        <div className="shrink-0 border-t p-3">
          <div className="mb-2 flex items-center justify-between gap-2">
            <div className="flex min-w-0 items-center gap-2.5">
              <div className="flex size-8 shrink-0 items-center justify-center rounded-full bg-gradient-to-br from-primary to-primary/60 text-xs font-semibold text-primary-foreground">
                {(user?.nickname || user?.username || '未登录').charAt(0).toUpperCase()}
              </div>
              <div className="min-w-0">
                <p className="truncate text-xs font-medium">{user?.nickname || user?.username || '未登录'}</p>
                <p className="truncate text-[10px] text-muted-foreground">{user?.role || '安全运营'}</p>
              </div>
            </div>
            <button
              type="button"
              onClick={handleLogout}
              title="退出登录"
              className="flex size-7 shrink-0 items-center justify-center rounded-none text-muted-foreground transition-colors hover:bg-destructive/10 hover:text-destructive"
            >
              <LogOut className="size-3.5" />
            </button>
          </div>
          <div className="flex items-center justify-between px-1">
            <button
              type="button"
              className="flex items-center gap-1 text-xs text-muted-foreground hover:text-foreground"
              onClick={() => toast.info('设置功能开发中')}
            >
              <Settings className="size-3.5" />
              设置
            </button>
          </div>
        </div>
      </aside>

      {/* 内容区 */}
      <div className="flex min-w-0 flex-1 flex-col">
        <header className="flex h-14 shrink-0 items-center justify-between border-b bg-background/80 px-6 backdrop-blur">
          <h1 className="text-sm font-semibold">{pageTitle(pathname)}</h1>
          <div className="flex items-center gap-1.5">
            <Button variant="ghost" size="icon-sm" title="刷新" onClick={() => location.reload()}>
              <RefreshCw className="size-4" />
            </Button>
          </div>
        </header>
        <main className="flex-1 overflow-y-auto p-6">
          <Outlet />
        </main>
      </div>
    </div>
  )
}
