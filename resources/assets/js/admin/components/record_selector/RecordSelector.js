import React from 'react';

export default class RecordSelector extends React.Component
{
    constructor()
    {
        super();
        this.state = {keyword: '', loading: false, results: [], active_value: null, show_results: false};
    }

    componentWillMount()
    {
        if(this.props.initial_value)
        {
            this.setState({active_value: {id: this.props.initial_value.id, label: this.props.initial_value.label}, keyword: this.props.initial_value.label})
        }
    }

    doSearch(e)
    {
        e.preventDefault();
        this.setState({loading: true, results: [], show_results: false});

        $.ajax({
            url: '/admin/ajax/record-search/' + this.props.record_type + '?keyword=' + encodeURIComponent(this.state.keyword),
            method: 'GET',
            dataType: 'json',
            timeout: 3000,
            complete: (res) => {
                if(res.status == 200)
                {
                    if(res.responseJSON.errors)
                    {
                        this.setState({loading: false, results: [], show_results: false});
                    }
                    else
                    {
                        if(res.responseJSON.results.length > 0)
                            this.setState({results: res.responseJSON.results, keyword: '', loading: false, show_results: true});
                        else
                            this.setState({loading: false, results: [], show_results: false});
                    }
                }
                else if(res.status == 0)
                {
                    // Todo: handle timeout
                    this.setState({loading: false, results: [], show_results: false});
                }
            }
        });
    }

    selectSearchResult(e)
    {
        e.preventDefault();
        let idx = $(e.target).data('idx');
        let search_result = this.state.results[idx];

        if(this.props.updateHandler)
        {
            this.props.updateHandler(search_result, this.props.name, this.props.record_type);
        }

        this.setState({active_value: search_result, show_results: false, results: []});
        $(e.target).parents('.record-selector-container').find('.keyword-field').val('');
    }

    cancelSearch(e)
    {
        e.preventDefault();
        this.setState({show_results: false, results: []});
    }

    deleteActiveValue(e)
    {
        e.preventDefault();
        this.setState({active_value: null, keyword: ''});

        if(this.props.updateHandler)
        {
            this.props.updateHandler({id: null, label: null}, this.props.name, this.props.record_type);
        }
    }
    
    render()
    {
        let results_element = null;
        let search_bar = null;
        if(this.state.show_results && this.state.results)
        {
            if(this.state.results.length > 0)
            {
                let idx = -1;
                let result_lines = this.state.results.map(r => {
                    idx++;
                    return(
                        <li key={r.id}>
                            <a style={{marginBottom: 6, backgroundColor: 'rgb(156, 39, 176)', borderRadius: 5, color: 'white', padding: '3px 12px', fontWeight: 'bold', display: 'block'}} href='' data-idx={idx} onClick={this.selectSearchResult.bind(this)}>
                                Id:{r.id} {r.label}
                            </a>
                        </li>
                    )
                });

                results_element = (
                    <div style={{marginTop: 8, border: '1px solid #bfbfbf', overflowY: 'auto', maxHeight: 145, padding: 13}} className="search-results">
                        <ul style={{listStyle: 'none', padding: 0, margin: 0}}>{result_lines}</ul>
                    </div>
                );

                search_bar = (
                    <div style={{marginBottom: 5}}>
                        <button onClick={this.cancelSearch.bind(this)} style={{fontSize: '10px', padding: '3px 12px', fontWeight: 'bold'}} className="btn btn-danger"><i className="fa fa-times"/> Cancel Search</button>
                    </div>
                );
            }
        }
        else
        {
            search_bar = (
                <div>
                    <input type="text" placeholder={'Select ' +  this.props.label}
                           className="keyword-field"
                           value={this.state.keyword}
                           onChange={e => { this.setState({keyword: e.target.value}) }}
                           style={{border: '1px solid #bfbfbf', marginRight: 3, display: 'inline-block', lineHeight: 30, fontSize: 12, padding: '4px 7px', width: 180, borderRadius: 4, height: 32}} />

                    {this.state.loading ? null : <button onClick={this.doSearch.bind(this)} style={{display: 'inline-block'}} className="btn btn-info"><i className="fa fa-search"/></button>}
                    {this.state.active_value ? <button onClick={this.deleteActiveValue.bind(this)} className="btn btn-danger"><i className="fa fa-eraser"/></button> : null}
                </div>
            );
        }

        // Show name display
        let name_display = null;

        if(!this.state.loading)
        {
            if(this.state.active_value)
            {
                name_display = (<div style={{marginBottom: 0, fontSize: 12, fontWeight: 'bold'}} className={'record_selector_label_' + this.props.record_type}>
                    Id:{this.state.active_value.id} | {this.state.active_value.label}
                </div>);
            }
            else
            {
                name_display = (<div style={{marginBottom: 0, fontSize: 12, fontWeight: 'bold'}} className={'record_selector_label_' + this.props.record_type}>No record selected.</div>)
            }
        }

        // Show loading message
        let loading_message = (this.state.loading) ?
            (<div style={{marginBottom: 7}}><i className="fa fa-hourglass"/> Loading...Please Wait</div>) : null;


        return(
                <div style={{border: '1px solid #bfbfbf', position: 'relative', marginBottom: 5, padding: 14, borderRadius: 4}} className="record-selector-container">
                    {loading_message}

                    {this.state.loading ? null : <h5 style={{fontSize: 15, marginTop: 0, marginBottom: 2}}>{'Select ' + this.props.label}</h5>}

                    {name_display}
                    {search_bar}
                    <input className="record-selector-data" data-record-type={this.props.record_type}
                           value={this.state.active_value ? this.state.active_value.id : ''}
                           readOnly="readonly"
                           name={'record_selector_data_' + this.props.name} type="text" style={{display: 'none'}} />
                    {results_element}
                </div>
        );
    }
}