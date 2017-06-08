/**
 * Class definition of Article
 * @author Christopher Reeves <chrisreeves12@yahoo.com>
 */

import React from 'react';
import ReactDOM from 'react-dom';
import Input from '../../forms/Input';
import Util from '../../../core/Util';
import Popup from '../../../core/Popup';

export default class ArticleForm extends React.Component {
    constructor(props) {
        super(props);
        let state = {};

        if(props.initial_data.id)
        {
            let data = props.initial_data;
            data.body = atob(data.view_body);
            state = props.initial_data;
        }
        else
        {
            state = {
                title: '',
                summary: '',
                article_category_id: '',
                body: '',
                slug: '',
                is_published: false,
            }
        }

        state.article_categories = props.article_categories;
        state.errors = [];
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

    componentDidMount()
    {
        CKEDITOR.replace('article_body', {
            height: 800
        });
    }

    previewArticle(e)
    {
        e.preventDefault();
        this.state.body = CKEDITOR.instances.article_body.getData();
        this.state.errors = [];
        this.setState(this.state);

        $.ajax({
            url: '/article/preview',
            method: 'POST',
            dataType: 'json',
            data: {data: this.state, _token: Util.get_auth_token()},
            complete: (res) => {
                if(res.status == 200)
                {
                    if(res.responseJSON.errors)
                    {
                        this.setState({errors: res.responseJSON.errors});
                    }
                    else
                    {
                        // Open a new window to place HTML into
                        let w = window.open();
                        if(w)
                        {
                            w.document.write(res.responseJSON.output);
                            w.document.close();
                        }
                        else
                        {
                            // Couldn't create window, may be due to popup blocker
                            let popup = new Popup();
                            popup.show("Please make sure you disable the Popup Blocker so that you can see the preview.");
                        }
                    }
                }
            }
        });
    }

    doSave(e)
    {
        e.preventDefault();
        this.state.body = CKEDITOR.instances.article_body.getData();
        this.state.errors = [];
        this.setState(this.state);
        let errors = [];

        $.ajax({
            url: '/admin/record/Article/' + (this.state.id || ''),
            method: (this.state.id) ? 'PUT' : 'POST',
            dataType: 'json',
            data: {data: this.state, _token: Util.get_auth_token()},
            complete: (res) => {
                if(res.status == 200)
                {
                    if(res.responseJSON.errors)
                    {
                        errors = res.responseJSON.errors;
                        this.setState({errors});
                    }
                    else if(res.responseJSON.system_error)
                    {
                        let popup = new Popup();
                        popup.show(res.responseJSON.system_error);
                    }
                    else
                    {
                        window.location = '/admin/article/' + res.responseJSON.id;
                    }
                }
            }
        });
    }

    updateSelectList(get_url)
    {
        $.ajax({
            url: get_url,
            data: {_token: Util.get_auth_token()},
            dataType: 'json',
            timeout: 3000,
            complete: (res) => {
                if(res.status == 200)
                {
                    let state = {};
                    state['article_categories'] = res.responseJSON.records;
                    this.setState(state);
                }
                else if(res.status == 0)
                {
                    // Todo: handle timeout
                }
            }
        });
    }

    render() {
        return (
            <div>
                <div style={{marginBottom: '32px'}}>
                    <a href="/admin/article" className="btn btn-secondary"><i className="fa fa-pencil-square"/> Create New</a>
                    <a href="/admin/list/article" className="btn btn-secondary"><i className="fa fa-list"/> List All Articles</a>
                </div>
                <form onSubmit={this.doSave.bind(this)}>
                    <Input is_required="true" errors={this.state.errors} updateValueHandler={this.updateValueHandler.bind(this)} value={this.state.title} name="title" label="Title" type="text"/>
                    <Input is_required="true" errors={this.state.errors} updateValueHandler={this.updateValueHandler.bind(this)} value={this.state.slug} name="slug" label="Slug" type="text"/>

                    <Input is_required="true" error_key='article_category' errors={this.state.errors} updateValueHandler={this.updateValueHandler.bind(this)}
                           create_links={{create_label: 'Add New Article Category', create_model: 'ArticleCategory', create_url: '/admin/article-category'}}
                           updateListHandler={this.updateSelectList.bind(this)}
                           value={this.state.article_category_id} options={this.state.article_categories}
                           name="article_category_id" label="Article Category" type="select"/>

                    <Input errors={this.state.errors} updateValueHandler={this.updateValueHandler.bind(this)} value={this.state.summary} name="summary" label="Summary" type="textarea"/>
                    <Input updateValueHandler={this.updateValueHandler.bind(this)} value={this.state.is_published} name="is_published" label="Is Published?" type="checkbox"/>
                    <textarea name="body" defaultValue={this.state.body} id="article_body"/>
                    <button style={{marginTop: 15}} className="btn btn-primary"><i className="fa fa-save"/> Save Article</button> <button onClick={this.previewArticle.bind(this)} style={{marginTop: 15}} className="btn btn-info"><i className="fa fa-eye"/> Preview Article</button>
                </form>
            </div>
        );
    }

    static initialize()
    {
        let element = document.getElementById('article_page_section');
        if(element)
            ReactDOM.render(<ArticleForm article_categories={JSON.parse(element.dataset.articleCategories)} initial_data={JSON.parse(element.dataset.initialData)}/>, element);
    }
}