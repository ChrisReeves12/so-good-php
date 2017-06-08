/**
 * Class definition of AdminListSearch
 * @author Christopher Reeves <chrisreeves12@yahoo.com>
 */

import React from 'react';
import ReactDOM from 'react-dom';
import Popup from '../../../core/Popup';

export default class AdminListSearch extends React.Component {
    constructor(props) {
        super(props);
        this.state = {keyword: ''};
    }

    doKeywordUpdate(e)
    {
        this.setState({keyword: e.target.value});
    }

    doSearch(e)
    {
        e.preventDefault();

        if(this.state.keyword !== '')
        {
            $.ajax({
                url: '/admin/list/record/search',
                method: 'GET',
                dataType: 'json',
                data: {keyword: this.state.keyword, type: this.props.list_type},
                complete: (res) => {
                    if(res.status == 200)
                    {
                        $('#search_results_modal').html(res.responseText);
                        $('#search_results_modal').modal();
                    }
                }
            });
        }
    }

    render() {
        return(
            <form onSubmit={this.doSearch.bind(this)}>
                <div className="row list-search-section">
                    <div className="col-lg-6">
                        <div className="input-group">
                            <input value={this.state.keyword} onChange={this.doKeywordUpdate.bind(this)} type="text" class="form-control SearchBar" placeholder={'Search ' + this.props.list_name}/>
                            <span className="input-group-btn">
                                <button className="btn btn-primary SearchButton" type="submit">
                                    <i className="glyphicon glyphicon-search SearchIcon"/> Search
                                </button>
                            </span>
                        </div>
                    </div>
                </div>
            </form>
        );
    }

    static initialize()
    {
        let element = document.getElementById('admin_list_search');
        if(element)
            ReactDOM.render(<AdminListSearch list_type={element.dataset.listType} list_name={element.dataset.listName}/>, element);
    }
}