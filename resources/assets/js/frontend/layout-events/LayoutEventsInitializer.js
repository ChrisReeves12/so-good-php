import DeleteLinkHandler from './DeleteLinkHandler';
import MobileMenuToggleHandler from './MobileMenuToggleHandler';
import PaginationWidget from '../react/components/pagination_widget/PaginationWidget';

export default class LayoutEventsInitializer
{
    static initialize()
    {
        DeleteLinkHandler.initialize();
        MobileMenuToggleHandler.initialize();
        PaginationWidget.initialize();
    }
}