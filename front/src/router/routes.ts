import HomeView from '../views/HomeView.vue';

const routes = {
    home: {
        path: '/',
        component: HomeView,
    },
    current_expenses: {
        path: '/current-expenses',
        component: () => import('@/domains/Expense/view/ExpenseView.vue'),
    },
    category_list: {
        path: '/category-list',
        component: () => import('@/domains/Category/view/CategoryListView.vue'),
    },
    prepare_budget: {
        path: '/prepare-budget',
        component: () => import('@/domains/Budget/view/PrepareBudgetView.vue'),
    },
    irregular_expenses: {
        path: '/irregular-expenses',
        component: () => import('@/domains/IrregularExpense/view/IrregularExpenseListView.vue'),
    },
    people: {
        path: '/people',
        component: () => import('@/domains/Person/view/PersonListView.vue'),
    },
};

export default routes;