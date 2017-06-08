import React from 'react';
import ReactDOM from 'react-dom';
import ParentCategory from './ParentCategory';
import Util from '../../../core/Util';
import Popup from '../../../core/Popup';

export default class ProductCategoryListComponent extends React.Component {
    componentWillMount() {
        this.setState({parent_categories: this.props.initial_data});
    }

    doDeleteCategory(idx, id, is_child) {
        let parent_categories = this.state.parent_categories.slice();

        $.ajax({
            url: `/admin/record/ProductCategory/${id}`,
            method: 'DELETE',
            dataType: 'json',
            data: {_token: Util.get_auth_token()},
            timeout: 3000,
            complete: (res) => {
                if(res.status == 200) {
                    if(res.responseJSON.system_error)
                    {
                        let popup = new Popup();
                        popup.show(res.responseJSON.system_error);
                    }
                    else
                    {
                        // No need to refresh the page if the category has no children
                        if(parent_categories[idx].children.length == 0 && !is_child) {
                            parent_categories.splice(idx, 1);
                            this.setState({parent_categories});
                        }
                        else {
                            window.location.reload();
                        }
                    }
                }
                else if(res.status == 0) {
                    alert('Timed out while deleting category, please try again.');
                }
            }
        });
    }

    renderListItems() {
        let idx = -1;
        return this.state.parent_categories.map(pc => {
            idx++;
            return (
                <ParentCategory deleteHandler={this.doDeleteCategory.bind(this)} idx={idx} key={pc.id} data={pc}/>
            )
        });
    }

    render() {
        return (
            <div className="product-cat-list">
                {this.renderListItems()}
            </div>
        );
    }

    static initialize() {
        let element = document.getElementById('product_category_list');
        if(element) {
            ReactDOM.render(<ProductCategoryListComponent
                initial_data={JSON.parse(element.dataset.initialData)}/>, element);
        }
    }
}
