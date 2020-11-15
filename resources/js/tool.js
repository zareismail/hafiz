Nova.booting((Vue, router, store) => {
  router.addRoutes([
    {
      name: 'hafiz',
      path: '/hafiz',
      component: require('./components/Tool'),
    },
  ])
})
