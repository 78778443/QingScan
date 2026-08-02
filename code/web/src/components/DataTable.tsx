import type { ReactNode } from 'react'
import {
  Table,
  TableBody,
  TableCell,
  TableHead,
  TableHeader,
  TableRow,
} from '@/components/ui/table'
import { Badge } from '@/components/ui/badge'
import { Skeleton } from '@/components/ui/skeleton'
import {
  Pagination,
  PaginationContent,
  PaginationEllipsis,
  PaginationItem,
  PaginationLink,
  PaginationNext,
  PaginationPrevious,
} from '@/components/ui/pagination'
import { cn } from '@/lib/utils'
import { Inbox } from 'lucide-react'

export interface Column<T = Record<string, unknown>> {
  key: string
  header: ReactNode
  className?: string
  render?: (row: T) => ReactNode
}

interface DataTableProps<T = Record<string, unknown>> {
  columns: Column<T>[]
  rows: T[]
  loading?: boolean
  total?: number
  currentPage?: number
  totalPage?: number
  onPageChange?: (page: number) => void
  toolbar?: ReactNode
  rowKey?: (row: T) => string | number
  rowClassName?: (row: T) => string | undefined
  emptyText?: string
}

function pageItems(current: number, total: number): (number | '…')[] {
  if (total <= 7) return Array.from({ length: total }, (_, i) => i + 1)
  const items: (number | '…')[] = [1]
  const start = Math.max(2, current - 1)
  const end = Math.min(total - 1, current + 1)
  if (start > 2) items.push('…')
  for (let i = start; i <= end; i++) items.push(i)
  if (end < total - 1) items.push('…')
  items.push(total)
  return items
}

export function DataTable<T = Record<string, unknown>>({
  columns,
  rows,
  loading,
  total,
  currentPage = 1,
  totalPage = 1,
  onPageChange,
  toolbar,
  rowKey,
  rowClassName,
  emptyText = '暂无数据',
}: DataTableProps<T>) {
  return (
    <div className="space-y-4">
      {toolbar}
      <div className="rounded-lg border shadow-sm">
        <Table>
          <TableHeader>
            <TableRow className="bg-muted/60">
              {columns.map((col) => (
                <TableHead
                  key={col.key}
                  className={cn('whitespace-nowrap text-xs font-semibold text-muted-foreground', col.className)}
                >
                  {col.header}
                </TableHead>
              ))}
            </TableRow>
          </TableHeader>
          <TableBody>
            {loading ? (
              Array.from({ length: 3 }).map((_, i) => (
                <TableRow key={`skeleton-${i}`}>
                  <TableCell colSpan={columns.length} className="h-10 px-4">
                    <div className="flex items-center gap-4">
                      <Skeleton className="h-3.5 w-1/3" />
                      <Skeleton className="h-3.5 w-1/4" />
                      <Skeleton className="h-3.5 w-1/5" />
                    </div>
                  </TableCell>
                </TableRow>
              ))
            ) : rows.length === 0 ? (
              <TableRow>
                <TableCell colSpan={columns.length} className="py-16 text-center">
                  <div className="flex flex-col items-center gap-2">
                    <Inbox className="size-10 text-muted-foreground/50" />
                    <p className="text-sm text-muted-foreground">{emptyText}</p>
                    <p className="text-xs text-muted-foreground/70">没有匹配的记录，可调整筛选条件后重试</p>
                  </div>
                </TableCell>
              </TableRow>
            ) : (
              rows.map((row, i) => (
                <TableRow
                  key={rowKey ? rowKey(row) : i}
                  className={cn('transition-colors hover:bg-accent/50', rowClassName?.(row))}
                >
                  {columns.map((col) => (
                    <TableCell key={col.key} className={cn('align-middle text-[13px]', col.className)}>
                      {col.render ? col.render(row) : String((row as Record<string, unknown>)[col.key] ?? '-')}
                    </TableCell>
                  ))}
                </TableRow>
              ))
            )}
          </TableBody>
        </Table>
      </div>
      {totalPage > 1 && (
        <Pagination>
          <PaginationContent>
            <PaginationItem>
              <PaginationPrevious
                href="#"
                className={cn(
                  'hover:bg-primary/10 hover:text-primary',
                  currentPage <= 1 && 'pointer-events-none opacity-50',
                )}
                onClick={(e) => {
                  e.preventDefault()
                  if (currentPage > 1) onPageChange?.(currentPage - 1)
                }}
              />
            </PaginationItem>
            {pageItems(currentPage, totalPage).map((item, i) =>
              item === '…' ? (
                <PaginationItem key={`e${i}`}>
                  <PaginationEllipsis />
                </PaginationItem>
              ) : (
                <PaginationItem key={item}>
                  <PaginationLink
                    href="#"
                    isActive={item === currentPage}
                    className={
                      item === currentPage
                        ? 'bg-primary text-primary-foreground hover:bg-primary/90 hover:text-primary-foreground'
                        : 'hover:bg-primary/10 hover:text-primary'
                    }
                    onClick={(e) => {
                      e.preventDefault()
                      onPageChange?.(item)
                    }}
                  >
                    {item}
                  </PaginationLink>
                </PaginationItem>
              ),
            )}
            <PaginationItem>
              <PaginationNext
                href="#"
                className={cn(
                  'hover:bg-primary/10 hover:text-primary',
                  currentPage >= totalPage && 'pointer-events-none opacity-50',
                )}
                onClick={(e) => {
                  e.preventDefault()
                  if (currentPage < totalPage) onPageChange?.(currentPage + 1)
                }}
              />
            </PaginationItem>
          </PaginationContent>
        </Pagination>
      )}
      {typeof total === 'number' && total > 0 && (
        <p className="text-xs text-muted-foreground">共 {total} 条记录，第 {currentPage} / {totalPage} 页</p>
      )}
    </div>
  )
}

// ---------- 公共 Badge 色板 ----------

const SEVERITY_COLORS: Record<string, string> = {
  low: 'bg-green-500/15 text-green-600 dark:text-green-400',
  medium: 'bg-yellow-500/15 text-yellow-600 dark:text-yellow-400',
  high: 'bg-orange-500/15 text-orange-600 dark:text-orange-400',
  critical: 'bg-red-500/15 text-red-600 dark:text-red-400',
}

/** 严重级别色板：支持 low/medium/high/critical 与中文 低危/中危/高危/严重 */
export function severityColor(level: string): string {
  const key = (level ?? '').trim().toLowerCase()
  if (key === '低危') return SEVERITY_COLORS.low
  if (key === '中危') return SEVERITY_COLORS.medium
  if (key === '高危') return SEVERITY_COLORS.high
  if (key === '严重') return SEVERITY_COLORS.critical
  return SEVERITY_COLORS[key] ?? 'bg-muted text-muted-foreground'
}

/** 通用状态 Badge：0=待处理(灰) / 1=正常(绿) / 2=完成(蓝)，其余值原样展示 */
export function statusBadge(v: unknown) {
  const raw = String(v ?? '').trim()
  const meta: Record<string, { label: string; cls: string }> = {
    '0': { label: '待处理', cls: 'bg-muted text-muted-foreground' },
    '1': { label: '正常', cls: SEVERITY_COLORS.low },
    '2': { label: '完成', cls: 'bg-blue-500/15 text-blue-600 dark:text-blue-400' },
  }
  const m = meta[raw]
  return m ? <Badge className={m.cls}>{m.label}</Badge> : <Badge variant="outline">{raw || '-'}</Badge>
}
