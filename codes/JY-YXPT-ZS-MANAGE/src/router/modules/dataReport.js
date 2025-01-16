import Layout from '@/layout'

const dataReport = {
  path: '/dataReport',
  name: 'dataReport',
  meta: {
    title: '数据报表',
    icon: 'el-icon-s-data',
    roles: [
      'admin/statistics.Statistics/customerStatistics',
      'admin/statistics.Statistics/salesStatistics',
    ]
  },
  redirect: 'noRedirect',
  component: Layout,
  alwaysShow: true,
  children: [{
      path: 'clientStats',
      name: 'ClientStats',
      meta: {
        title: '客户统计',
        roles: [
          'admin/statistics.Statistics/customerStatistics',
        ]
      },
      component: () => import('@/views/dataReport/clientStats')
    },
    {
      path: 'salesStats',
      name: 'SalesStats',
      meta: {
        title: '销售统计',
        roles: [
          'admin/statistics.Statistics/salesStatistics',
        ]
      },
      component: () => import('@/views/dataReport/salesStats')
    }
  ]
}
export default dataReport
