import type { ReactNode } from 'react'
import {
  Table,
  TableBody,
  TableCell,
  TableHead,
  TableHeader,
  TableRow,
} from '@/components/ui/table'
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
  emptyText = '暂无数据',
}: DataTableProps<T>) {
  return (
    <div className="space-y-4">
      {toolbar}
      <div className="rounded-lg border">
        <Table>
          <TableHeader>
            <TableRow className="bg-muted/50">
              {columns.map((col) => (
                <TableHead key={col.key} className={cn('whitespace-nowrap', col.className)}>
                  {col.header}
                </TableHead>
              ))}
            </TableRow>
          </TableHeader>
          <TableBody>
            {loading ? (
              <TableRow>
                <TableCell colSpan={columns.length} className="h-24">
                  <div className="flex items-center gap-4">
                    <Skeleton className="h-4 w-1/3" />
                    <Skeleton className="h-4 w-1/4" />
                    <Skeleton className="h-4 w-1/5" />
                  </div>
                </TableCell>
              </TableRow>
            ) : rows.length === 0 ? (
              <TableRow>
                <TableCell colSpan={columns.length} className="h-24 text-center text-muted-foreground">
                  {emptyText}
                </TableCell>
              </TableRow>
            ) : (
              rows.map((row, i) => (
                <TableRow key={rowKey ? rowKey(row) : i}>
                  {columns.map((col) => (
                    <TableCell key={col.key} className={cn('align-middle', col.className)}>
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
                className={cn(currentPage <= 1 && 'pointer-events-none opacity-50')}
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
                className={cn(currentPage >= totalPage && 'pointer-events-none opacity-50')}
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
        <p className="text-sm text-muted-foreground">共 {total} 条记录，第 {currentPage} / {totalPage} 页</p>
      )}
    </div>
  )
}
