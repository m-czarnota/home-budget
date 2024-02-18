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
    prepare_budget: {
        path: '/prepare-budget',
        component: () => import('../views/PrepareBudgetView.vue'),
    },
    irregular_expenses: {
        path: '/irregular-expenses',
        component: () => import('../views/IrregularExpensesView.vue')
    }
};

export default routes;