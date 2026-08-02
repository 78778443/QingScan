import { Routes, Route, Navigate } from 'react-router-dom'
import AppLayout from '@/layouts/AppLayout'
import LoginPage from '@/pages/Login'
import Dashboard from '@/pages/Dashboard'
import Targets from '@/pages/webscan/Targets'
import ScanResults from '@/pages/webscan/ScanResults'
import Assets from '@/pages/asm/Assets'
import CodeAudit from '@/pages/code/CodeAudit'
import Workorders from '@/pages/workorder/Workorders'

export default function App() {
  return (
    <Routes>
      <Route path="/login" element={<LoginPage />} />
      <Route path="/" element={<AppLayout />}>
        <Route index element={<Dashboard />} />
        <Route path="webscan/targets" element={<Targets />} />
        <Route path="webscan/:tool" element={<ScanResults />} />
        <Route path="asm/:kind" element={<Assets />} />
        <Route path="code" element={<CodeAudit />} />
        <Route path="workorder" element={<Workorders />} />
      </Route>
      <Route path="*" element={<Navigate to="/" replace />} />
    </Routes>
  )
}
