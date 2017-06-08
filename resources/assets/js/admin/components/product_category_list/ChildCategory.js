import React from 'react';

export default class ChildCategory extends React.Component
{
    constructor()
    {
        super();
        this.state = {expanded: false, cached_children: []};
    }

    getChildren(e)
    {
        e.preventDefault();
        if(!this.state.expanded)
        {
            if(this.state.cached_children.length == 0)
            {
                $.ajax({
                    url: '/admin/product-category/list/get-child-categories',
                    method: 'GET',
                    dataType: 'json',
                    data: {id: this.props.data.id},
                    timeout: 3000,
                    complete: (res) => {
                        if(res.status == 200)
                        {
                            this.setState({expanded: !this.state.expanded, cached_children: res.responseJSON.children});
                        }
                        else if(res.status == 0)
                        {
                          alert('Timed out while fetching categories, please try again.');
                        }
                    }
                });
            }
            else
            {
                this.setState({expanded: !this.state.expanded});
            }
        }
        else
        {
            this.setState({expanded: !this.state.expanded});
        }
    }

    renderChildren()
    {
        let ret_val = null;
        if(this.state.cached_children.length > 0)
        {
            let idx = -1;
            ret_val = this.state.cached_children.map(child_cat => {
                idx++;
                return(<ChildCategory deleteHandler={this.props.deleteHandler} idx={idx} key={child_cat.id} data={child_cat}/>);
            });
        }

        return ret_val;
    }

    handleDelete(e)
    {
      e.preventDefault();
      this.props.deleteHandler(this.props.idx, this.props.data.id, true);
    }

    render()
    {
        let data = this.props.data;
        let cat_url = `/admin/product-category/${data.id}`;

        return(
            <div style={{marginLeft: 30}}>
                <div className="category-list-child-category">
                    <a href='' onClick={this.handleDelete.bind(this)} className="delete-button"><i className="fa fa-times"/></a>
                    {data.image ? <div className="cat-photo">
                        <a href={cat_url}><img src={data.image.href}/></a>
                    </div> : null}
                    <div className="category-name-section">
                        <h6 className="cat-name">
                            <a href={cat_url}>
                                {data.name + ' ' + (data.is_inactive ? '(Inactive)' : '')}
                            </a>
                        </h6>
                        <div className="cat-id">ID: {data.id}</div>
                    </div>
                    {data.child_count > 0 ?
                        <a onClick={this.getChildren.bind(this)} className="cat-expand" href="">
                            <i className={`fa ${this.state.expanded ? 'fa-minus' : 'fa-plus'}`}/></a> : null}
                </div>
                {this.state.expanded && this.state.cached_children.length > 0 ? this.renderChildren() : null}
            </div>
        )
    }
}
