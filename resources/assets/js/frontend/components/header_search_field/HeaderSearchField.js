import React from 'react';
import ReactDOM from 'react-dom';
import Util from '../../../core/Util';

export default class HeaderSearchField extends React.Component
{
    constructor()
    {
        super();
        this.state = {keyword: '', docs: [], selected_serp: -1};
        this.update_timer_active = false;
        this.timer = null;
    }

    componentWillMount()
    {
        $(document).on('click', (e) => {
            this.setState({docs: [], selected_serp: -1});
        });
    }

    doSubmit(e)
    {
        e.preventDefault();

        // Take user directly to product
        if(this.state.docs.length > 0 && this.state.selected_serp > -1)
        {
            window.location = `/${this.state.docs[this.state.selected_serp].slug}`;
        }
        else
        {
            // Go to product search results page
            window.location = `/site-search?keyword=${this.state.keyword}`;
        }
    }

    doUpdateKeyword(e)
    {
        this.setState({keyword: e.target.value, selected_serp: -1});

        if(this.update_timer_active)
            clearTimeout(this.timer);


        // Load search results
        this.timer = setTimeout(() => {

            if(this.state.keyword !== '')
            {
                $.ajax({
                    url: '/ajax-search/products',
                    method: 'GET',
                    dataType: 'json',
                    timeout: 2000,
                    data: {keyword: this.state.keyword, start: 0, row_count: 7, },
                    complete: (res) => {
                        if(res.status === 200)
                        {
                            this.setState({docs: res.responseJSON.docs});
                        }
                    }
                });
            }

            this.update_timer_active = false;
        }, 200);

        this.update_timer_active = true;
    }

    doUpdateSelectedSerp(e)
    {
        if(e.keyCode === 40 && this.state.selected_serp < (this.state.docs.length - 1))
            this.setState({selected_serp: (this.state.selected_serp + 1)});
        else if(e.keyCode === 38 && this.state.selected_serp > -1)
            this.setState({selected_serp: (this.state.selected_serp - 1)});
    }

    getSearchResultLines()
    {
        let idx = -1;
        let cache_prefix = (Util.get_use_cache() === 'true') ? '/imagecache/30x40-0' : '';

        if(Array.isArray(this.state.docs) && this.state.docs.length > 0)
        {
            // Add the view more button
            return this.state.docs.map(doc => {
                idx++;
                let selected = (this.state.selected_serp === idx);

                return(
                    <div key={idx} className={'search-result ' + (selected ? 'selected' : '')}>
                        <a href={'/' + doc.slug}>
                            <img className="result-image" src={cache_prefix + doc.image} alt={doc.name} title={doc.name}/>
                            <div className="name-section">
                                <h5>{doc.name}</h5>
                                <p className="price">${doc.store_price.replace(',USD', '')}</p>
                            </div>
                        </a>
                    </div>
                );
            });
        }
        else
        {
            return null;
        }
    }

    render()
    {
        let search_result_lines = this.getSearchResultLines();

        return(
            <div>
                <div className="header-search">
                    <form className='search-form' onSubmit={this.doSubmit.bind(this)}>
                        <input value={this.state.keyword} onKeyDown={this.doUpdateSelectedSerp.bind(this)}
                               onChange={this.doUpdateKeyword.bind(this)} type='text' className="search-bar"
                               placeholder="Search"/>
                        <i onClick={this.doSubmit.bind(this)} className="fa fa-search"/>
                    </form>
                </div>
                {(search_result_lines && this.state.keyword) &&
                <div className="search-results">
                        {search_result_lines}</div>}
            </div>
        );
    }

    static initialize()
    {
        let elements = document.getElementsByClassName('header_search_field');

        for(let x = 0; x < elements.length; x++)
        {
            let element = elements[x];
            if(element)
                ReactDOM.render(<HeaderSearchField/>, element);
        }
    }
}