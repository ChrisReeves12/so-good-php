import React from 'react';
import ChildCategory from './ChildCategory';

export default class ParentCategory extends React.Component
{
    constructor()
    {
        super();
        this.state = {expanded: false};
    }

    renderChildren()
    {
        let idx = -1;
        return this.props.data.children.map(child => {
            idx++;
            return(
                <ChildCategory deleteHandler={this.props.deleteHandler} idx={idx} key={child.id} data={child}/>
            );
        });
    }

    handleDelete(e)
    {
        e.preventDefault();
        this.props.deleteHandler(this.props.idx, this.props.data.id, false);
    }

    render()
    {
        let data = this.props.data;
        let cat_url = `/admin/product-category/${data.id}`;

        return(
            <div>
                <div className="product-category-list-parent-item">
                    <a href='' onClick={this.handleDelete.bind(this)} className="delete-button"><i className="fa fa-times"/></a>
                    {data.image ? <div className="cat-photo">
                        <a href={cat_url}><img src={data.image.href}/></a>
                    </div> : null}
                    <div className="cat-name-section">
                        <h5 className="cat-name">
                            <a href={cat_url}>
                                {data.name + ' ' + (data.is_inactive ? '(Inactive)' : '')}
                            </a>
                        </h5>
                        <div className="cat-id">ID: {data.id}</div>
                    </div>
                    {this.props.data.children.length > 0 ?
                        <a className="cat-expand"
                           onClick={(e => {e.preventDefault(); this.setState({expanded: !this.state.expanded}) })}
                           href=""><i className={`fa ${this.state.expanded ? 'fa-minus' : 'fa-plus'}`}/></a> : null}
                </div>
                {this.state.expanded ? this.renderChildren() : null}
            </div>
        );
    }
}
