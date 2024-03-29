import HomeView from '../views/HomeView.vue';

const routes = {
    home: {
        path: '/',
        component: HomeView,
    },
    current_expenses: {
        path: '/current-expenses',
        component: () => import('../Expense/view/CurrentExpense.vue'),
    },
    category_list: {
        path: '/category-list',
        component: () => import('../Category/view/CategoryListView.vue'),
    },
    prepare_budget: {
        path: '/prepare-budget',
        component: () => import('../Budget/view/PrepareBudget.vue'),
    },
    irregular_expenses: {
        path: '/irregular-expenses',
        component: () => import('../Expense/view/IrregularExpenseList.vue'),
    },
    people: {
        path: '/people',
        component: () => import('../Person/view/PersonListView.vue'),
    },
};

export default routes;