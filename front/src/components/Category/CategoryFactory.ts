import { Category } from './Category';
import { SubCategory } from './SubCategory';

export class CategoryFactory {
    public static createFromResponse(response: Object, expandedDefault: Boolean = false) {
        const categories: Array<Category> = [];

        for (const [name, subcategories] of Object.entries(response)) {
            const category = new Category(name);
            category.subcategories = subcategories.map((subName: String) => new SubCategory(subName, category));
            
            if (expandedDefault) {
                category.switchExpand();
            }

            categories.push(category);
        }

        return categories;
    }
}