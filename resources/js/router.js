import { createRouter, createWebHistory } from 'vue-router'

const router = createRouter({
  history: createWebHistory(),
  routes: [
    {
      path: '/cabinet',
      name: 'cabinet',
      component: () => import('./components/Main/CabinetComponent.vue')
    },
    {
      path: '/',
      name: 'home',
      component: () => import('./components/Main/HomeComponent.vue')
    },
  ]
})

router.beforeEach((to, from, next) => {
  const token = localStorage.getItem('x_xsrf_token')

  if (!token) {
    if (to.name === 'user.login' || to.name === 'user.registration' || to.name === 'home' ) {
      return next()
    } else {
      return next({
        name: 'user.login'
      })
    }
  }
  if (to.name === 'user.login' || to.name === 'user.registration' && token) {
    return next({
      name: 'cabinet'
    })
  }

  next()
}) 

export default router