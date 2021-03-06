import BasePage from './components/BasePage.vue';
import LangdingPage from './pages/landing/Index.vue';
import DebutIndex from './pages/landing/DebutIndex.vue';
import Login from './pages/auth/Login.vue';
import Register from './pages/auth/Register.vue';

export default {
  mode: 'history',
  routes: [
    {
      path: '/',
      component: BasePage,
      name: 'BasePage',
      children: [
        {
          path: '/',
          component: LangdingPage,
          name: 'LangdingPage',
          meta: {
            requiresGuest: true,
          }
        },
        {
          path: '/login',
          component: Login,
          name: 'Login'
        },
        {
          path: '/register',
          component: Register,
          name: 'Register'
        },
        {
          path: '/debut',
          component: DebutIndex,
          meta: {
            requiresAuth: true
          }
        },
        {
          path: '/test',
          component: DebutIndex,
        },
      ]
    },
  ]
}
