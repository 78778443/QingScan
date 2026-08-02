import { useEffect } from 'react'
import { NavLink, Outlet, useLocation, useNavigate } from 'react-router-dom'
import { useQuery } from '@tanstack/react-query'
import { toast } from 'sonner'
import {
  LayoutDashboard,
  Globe,
  Radar,
  Shield,
  Database,
  Bug,
  FolderSearch,
  Server,
  Network,
  Layers,
  Link,
  FileSearch,
  AlertTriangle,
  ListTodo,
  Settings,
  LogOut,
  ExternalLink,
  type LucideIcon,
} from 'lucide-react'
import { apiGet, apiPost } from '@/lib/api'
import { cn } from '@/lib/utils'
import { Button } from '@/components/ui/button'

interface UserInfo {
  username?: string
  nickname?: string
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
      { label: 'xray', path: '/webscan/xray', icon: Radar },
      { label: 'nuclei', path: '/webscan/nuclei', icon: Shield },
      { label: 'sqlmap', path: '/webscan/sqlmap', icon: Database },
      { label: 'vulmap', path: '/webscan/vulmap', icon: Bug },
      { label: 'dirmap', path: '/webscan/dirmap', icon: FolderSearch },
      { label: 'whatweb', path: '/webscan/whatweb', icon: Globe },
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
    title: '漏洞结果',
    items: [
      { label: '插件扫描', path: '/result/plugin', icon: FileSearch },
      { label: '漏洞情报', path: '/result/vulnerable', icon: AlertTriangle },
    ],
  },
  { items: [{ label: '扫描任务', path: '/task', icon: ListTodo }] },
]

const TITLES: Record<string, string> = {
  '/': '控制台',
  '/webscan/targets': '网站扫描',
  '/webscan/xray': 'xray 扫描结果',
  '/webscan/nuclei': 'nuclei 扫描结果',
  '/webscan/sqlmap': 'sqlmap 扫描结果',
  '/webscan/vulmap': 'vulmap 扫描结果',
  '/webscan/dirmap': 'dirmap 扫描结果',
  '/webscan/whatweb': 'whatweb 扫描结果',
  '/asm/host': '主机资产',
  '/asm/port': '端口资产',
  '/asm/domain': '域名资产',
  '/asm/subdomain': '子域名',
  '/asm/url': 'URL资产',
  '/result/plugin': '插件扫描',
  '/result/vulnerable': '漏洞情报',
  '/task': '扫描任务',
}

function pageTitle(pathname: string): string {
  if (TITLES[pathname]) return TITLES[pathname]
  if (pathname.startsWith('/webscan/')) return '扫描结果'
  if (pathname.startsWith('/asm/')) return '资产管理'
  if (pathname.startsWith('/result/')) return '漏洞结果'
  if (pathname.startsWith('/task')) return '扫描任务'
  return 'QingScan'
}

function UserBadge({ user }: { user?: UserInfo }) {
  const name = user?.nickname || user?.username || '未登录'
  const initial = name.charAt(0).toUpperCase()
  return (
    <div className="flex min-w-0 items-center gap-2">
      <div className="flex size-7 shrink-0 items-center justify-center rounded-full bg-primary text-xs font-medium text-primary-foreground">
        {initial}
      </div>
      <span className="truncate text-xs">{name}</span>
    </div>
  )
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
        <div className="flex h-14 shrink-0 items-center gap-2 border-b px-4">
          <Shield className="size-5 text-primary" />
          <span className="text-sm font-semibold">QingScan</span>
        </div>

        <nav className="flex-1 overflow-y-auto px-3 py-3">
          {MENU.map((group, gi) => (
            <div key={gi} className="mb-4">
              {group.title && (
                <p className="mb-1 px-2 text-[11px] font-medium tracking-wider text-muted-foreground/80 uppercase">
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
                            'flex items-center gap-2 rounded-sm px-2 py-1.5 text-xs transition-colors',
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
          <div className="mb-2 flex items-center justify-between">
            <UserBadge user={user} />
            <Button variant="ghost" size="icon-sm" onClick={handleLogout} title="退出登录">
              <LogOut className="size-3.5" />
            </Button>
          </div>
          <div className="flex items-center justify-between px-1">
            <a
              href="http://127.0.0.1:8080/index/index"
              target="_blank"
              rel="noreferrer"
              className="flex items-center gap-1 text-xs text-muted-foreground hover:text-foreground"
            >
              <ExternalLink className="size-3.5" />
              旧版界面
            </a>
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
        <header className="flex h-14 shrink-0 items-center justify-between border-b px-6">
          <h1 className="text-sm font-semibold">{pageTitle(pathname)}</h1>
          <UserBadge user={user} />
        </header>
        <main className="flex-1 overflow-y-auto p-6">
          <Outlet />
        </main>
      </div>
    </div>
  )
}
