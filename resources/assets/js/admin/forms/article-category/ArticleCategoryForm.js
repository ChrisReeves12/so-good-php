/**
 * Class definition of ArticleCategory
 * @author Christopher Reeves <chrisreeves12@yahoo.com>
 */

import React from 'react';
import ReactDOM from 'react-dom';
import Input from '../../forms/Input';
import Util from '../../../core/Util';
import Popup from '../../../core/Popup';

export default class ArticleCategoryForm extends React.Component {
    constructor(props) {
        super(props);
        let state = {
            name: '',
            slug: ''
        };

        if(props.initial_data.id)
        {
            state = props.initial_data;
        }

        this.state = state;
    }

    updateValueHandler(field)
    {
        if($(field).is(':checkbox'))
        {
            this.state[field.name] = $(field).is(':checked');
        }
        else
        {
            this.state[field.name] = field.value;
        }

        this.setState(this.state);
    }

    doSave(e)
    {
        e.preventDefault();
        this.state.errors = [];
        this.setState(this.state);

        $.ajax({
            url: '/admin/record/ArticleCategory/' + (this.state.id || ''),
            dataType: 'json',
            method: (this.state.id) ? 'PUT' : 'POST',
            data: {data: this.state, _token: Util.get_auth_token()},
            complete: (res) => {
                let errors = [];

                if(res.status == 200)
                {
                    if(res.responseJSON.errors)
                    {
                        errors = res.responseJSON.errors;
                        this.setState({errors});
                    }
                    else if(res.responseJSON.system_error && res.responseJSON.system_error !== '')
                    {
                        let popup = new Popup();
                        popup.show(res.responseJSON.system_error);
                    }
                    else
                    {
                        window.location = '/admin/article-category/' + res.responseJSON.id;
                    }
                }
            }
        });
    }


    render() {
        return (
            <div>
                <div style={{marginBottom: '32px'}}>
                    <a href="/admin/article-category" className="btn btn-secondary"><i className="fa fa-pencil-square"/> Create New</a>
                    <a href="/admin/list/articleCategory" className="btn btn-secondary"><i className="fa fa-list"/> List All Article Categories</a>
                </div>
                <form onSubmit={this.doSave.bind(this)}>
                    <Input updateValueHandler={this.updateValueHandler.bind(this)} errors={this.state.errors} value={this.state.name} label="Name" name="name" type="text" is_required="true"/>
                    <Input updateValueHandler={this.updateValueHandler.bind(this)} errors={this.state.errors} value={this.state.slug} label="Slug" name="slug" type="text" is_required="true"/>
                    <button className="btn btn-primary"><i className="fa fa-save"/> Save Article Category</button>
                </form>
            </div>
        );
    }

    static initialize()
    {
        let element = document.getElementById('article_category_section');
        if(element)
            ReactDOM.render(<ArticleCategoryForm initial_data={JSON.parse(element.dataset.initialData)}/>, element);
    }
}