import { useState } from 'react'
import { Controller, useForm } from 'react-hook-form'
import { z } from 'zod'
import { Shield } from 'lucide-react'
import { toast } from 'sonner'
import { apiPost } from '@/lib/api'
import { Button } from '@/components/ui/button'
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card'
import { Checkbox } from '@/components/ui/checkbox'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'

const loginSchema = z.object({
  username: z.string().min(1, '请输入用户名'),
  password: z.string().min(1, '请输入密码'),
  remember_password: z.boolean().optional(),
})

type LoginFormValues = z.infer<typeof loginSchema>

export default function LoginPage() {
  const [submitting, setSubmitting] = useState(false)
  const {
    control,
    register,
    handleSubmit,
    setError,
    formState: { errors },
  } = useForm<LoginFormValues>({
    defaultValues: { username: '', password: '', remember_password: true },
  })

  const doLogin = async (values: LoginFormValues) => {
    setSubmitting(true)
    try {
      await apiPost('/auth/login', {
        username: values.username,
        password: values.password,
        remember_password: values.remember_password ? 1 : 0,
      })
      window.location.href = '/'
    } catch (err) {
      toast.error(err instanceof Error ? err.message : '登录失败')
    } finally {
      setSubmitting(false)
    }
  }

  const onSubmit = (values: LoginFormValues) => {
    const parsed = loginSchema.safeParse(values)
    if (!parsed.success) {
      for (const issue of parsed.error.issues) {
        const key = issue.path[0]
        if (key === 'username' || key === 'password') {
          setError(key, { message: issue.message })
        }
      }
      return
    }
    void doLogin(parsed.data)
  }

  return (
    <div className="flex min-h-screen items-center justify-center bg-gradient-to-br from-primary via-primary/85 to-primary/60 px-4">
      <Card className="w-full max-w-sm bg-card">
        <CardHeader className="items-center text-center">
          <div className="mb-1 flex size-12 items-center justify-center rounded-sm bg-primary text-primary-foreground">
            <Shield className="size-6" />
          </div>
          <CardTitle className="text-base">QingScan 安全运营平台</CardTitle>
        </CardHeader>
        <CardContent>
          <form className="space-y-4" onSubmit={handleSubmit(onSubmit)}>
            <div className="space-y-1.5">
              <Label htmlFor="username">用户名</Label>
              <Input id="username" placeholder="请输入用户名" autoComplete="username" {...register('username')} />
              {errors.username && <p className="text-xs text-destructive">{errors.username.message}</p>}
            </div>
            <div className="space-y-1.5">
              <Label htmlFor="password">密码</Label>
              <Input
                id="password"
                type="password"
                placeholder="请输入密码"
                autoComplete="current-password"
                {...register('password')}
              />
              {errors.password && <p className="text-xs text-destructive">{errors.password.message}</p>}
            </div>
            <div className="flex items-center gap-2">
              <Controller
                name="remember_password"
                control={control}
                render={({ field }) => (
                  <Checkbox
                    id="remember_password"
                    checked={field.value}
                    onCheckedChange={field.onChange}
                  />
                )}
              />
              <Label htmlFor="remember_password">记住密码</Label>
            </div>
            <Button type="submit" className="w-full" disabled={submitting}>
              {submitting ? '登录中...' : '登 录'}
            </Button>
            <p className="text-center text-xs text-muted-foreground">默认账号 admin</p>
          </form>
        </CardContent>
      </Card>
    </div>
  )
}
