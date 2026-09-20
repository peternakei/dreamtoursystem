export default [
  { path: '/logs', redirect: '/logs/activity/list' },
  ...['activity', 'requests', 'errors'].map(type => ({
    path: '/logs/' + type + '/list', name: 'logs-' + type,
    component: () => import('./LogsPage.vue'), props: { type }, meta: { title: 'System logs' },
  })),
]
