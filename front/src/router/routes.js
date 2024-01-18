import HomeView from '../views/HomeView.vue';

const routes = {
    home: {
        path: '/',
        component: HomeView,
    },
    expenses: {
        path: '/expenses',
        component: () => import('../views/ExpenseView.vue'),
    },
    category_list: {
        path: '/category-list',
        component: () => import('../views/CategoryView.vue'),
    },
};

export default routes;