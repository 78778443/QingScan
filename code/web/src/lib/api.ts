// 后端统一响应：{code, msg, data, count, total_page, current_page}
export interface ApiResp<T = unknown> {
  code: number
  msg: string
  data: T
  count?: number
  total_page?: number
  current_page?: number
}

export interface PageResp<T = Record<string, unknown>> {
  rows: T[]
  count: number
  total_page: number
  current_page: number
}

async function request<T>(url: string, options?: RequestInit): Promise<T> {
  const resp = await fetch(url, {
    headers: { 'Content-Type': 'application/json' },
    ...options,
  })
  if (!resp.ok) {
    throw new Error(`请求失败: ${resp.status} ${resp.statusText}`)
  }
  const json = (await resp.json()) as ApiResp<T>
  if (json.code !== 1) {
    throw new Error(json.msg || '请求失败')
  }
  return json.data
}

export function apiGet<T>(path: string, params?: Record<string, string | number | undefined>): Promise<T> {
  const qs = new URLSearchParams()
  if (params) {
    for (const [k, v] of Object.entries(params)) {
      if (v !== undefined && v !== '') qs.set(k, String(v))
    }
  }
  const suffix = qs.toString() ? `?${qs.toString()}` : ''
  return request<T>(`/api${path}${suffix}`)
}

export function apiPost<T>(path: string, body?: Record<string, unknown>): Promise<T> {
  return request<T>(`/api${path}`, { method: 'POST', body: JSON.stringify(body ?? {}) })
}

// 分页列表接口：后端返回 {code,msg,data: rows数组, count, total_page, current_page}
export async function apiPage<T>(path: string, params?: Record<string, string | number | undefined>): Promise<PageResp<T>> {
  const qs = new URLSearchParams()
  if (params) {
    for (const [k, v] of Object.entries(params)) {
      if (v !== undefined && v !== '') qs.set(k, String(v))
    }
  }
  const suffix = qs.toString() ? `?${qs.toString()}` : ''
  const resp = await fetch(`/api${path}${suffix}`)
  if (!resp.ok) throw new Error(`请求失败: ${resp.status} ${resp.statusText}`)
  const json = (await resp.json()) as ApiResp<T[]>
  if (json.code !== 1) throw new Error(json.msg || '请求失败')
  const rows = Array.isArray(json.data) ? json.data : []
  return {
    rows,
    count: json.count ?? rows.length,
    total_page: json.total_page ?? 1,
    current_page: json.current_page ?? 1,
  }
}
