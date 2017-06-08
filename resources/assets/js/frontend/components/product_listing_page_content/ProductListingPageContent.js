import React from 'react';
import ReactDOM from 'react-dom';
import MiniBuyBox from '../mini_buy_box/MiniBuyBox';
import Util from '../../../core/Util';
import PaginationControls from './PaginationControls';
import BrandFacets from './BrandFacets';
import PriceFacets from './PriceFacets';
import SubCategorySection from './SubCategorySection';

export default class ProductListingPageContent extends React.Component {

    constructor(props)
    {
        super(props);
        this.state = window.sogood.reactjs.listing_data;
    }

    doUpdatePage(e) {
        let state = Object.assign({}, this.state);
        state.page = e.target.value;

        this._doRequestNewProducts(state).then(res => {

            // Set new product listings
            state.products = res.products;
            state.num_of_pages = res.num_of_pages;
            state.num_of_listings = res.num_of_listings;
            this.setState(state);
        });
    }

    doUpdateSortOrder(e) {
        let state = Object.assign({}, this.state);
        state.sort_by = e.target.value;

        this._doRequestNewProducts(state).then(res => {

            // Set new product listings
            state.products = res.products;
            state.num_of_pages = res.num_of_pages;
            state.num_of_listings = res.num_of_listings;
            this.setState(state);
        });
    }

    doUpdatePriceFilter(value) {
        let state = Object.assign({}, this.state);
        state.price_filter = value;
        state.page = 1;

        this._doRequestNewProducts(state).then(res => {

            // Set new product listings
            state.products = res.products;
            state.num_of_pages = parseInt(res.num_of_pages);
            state.num_of_listings = parseInt(res.num_of_listings);
            this.setState(state);
        });
    }

    incCurrentPage(e) {
        e.preventDefault();
        if (this.state.page < this.state.num_of_pages) {
            let state = Object.assign({}, this.state);
            state.page = parseInt(state.page) + 1;

            this._doRequestNewProducts(state).then(res => {

                // Set new product listings
                state.products = res.products;
                state.num_of_pages = res.num_of_pages;
                state.num_of_listings = res.num_of_listings;
                this.setState(state);

                // Scroll to top if below certain point
                if ($(window).scrollTop() > 1000)
                    $('html, body').animate({scrollTop: 0}, 'slow');
            });
        }
    }

    decCurrentPage(e) {
        e.preventDefault();
        if (this.state.page > 1) {
            let state = Object.assign({}, this.state);
            state.page = parseInt(state.page) - 1;

            this._doRequestNewProducts(state).then(res => {

                // Set new product listings
                state.products = res.products;
                state.num_of_pages = res.num_of_pages;
                state.num_of_listings = res.num_of_listings;
                this.setState(state);

                // Scroll to top if below certain point
                if ($(window).scrollTop() > 1000)
                    $('html, body').animate({scrollTop: 0}, 'slow');
            });
        }
    }

    _doRequestNewProducts(data) {
        return $.get('', {
            list_type: data.list_type,
            keyword: data.keyword,
            num_of_listings: data.num_of_listings,
            num_of_pages: data.num_of_pages,
            page: data.page,
            sort_by: data.sort_by,
            slug: data.slug,
            price_filter: data.price_filter,
            is_json: true
        }, 'json');
    }

    render() {
        // Render list of products
        let idx = -1;
        let products = this.state.products.map(p => {
            idx++;
            return (
                <div key={p.id} className="col-sm-4">
                    <MiniBuyBox idx={idx} data={p}/>
                </div>
            );
        });

        // Render the page options
        let page_options = Util.fillArray(this.state.num_of_pages).map(i => {
            return (
                <option key={i} value={i + 1}>{'Page ' + (i + 1)}</option>
            );
        });

        let banner_image_url = '';
        if (this.state.banner) {
            banner_image_url = (Util.get_use_cache() === 'true') ? `/imagecache/825x272-0${this.state.banner}` : this.state.banner;
        }

        return (
            <div className="row">
                <div className="col-lg-3 facet-section">
                    <SubCategorySection list_type={this.state.list_type} keyword={this.state.keyword}
                                        title={this.state.title}
                                        num_of_listings={this.state.num_of_listings}
                                        sub_categories={this.state.sub_categories}/>
                    <div className="facet-section-block">
                        <PriceFacets price_filter={this.state.price_filter}
                                     doUpdatePriceFilter={this.doUpdatePriceFilter.bind(this)}/>
                    </div>
                    <div className="facet-section-block">
                        <BrandFacets brand_facets={this.state.brand_facets}/>
                    </div>
                </div>
                <div className="col-lg-9">
                    <div className="row product-listing-banner">
                        {this.state.banner ? <div className="col-sm-12">
                                <img className="img-fluid" src={banner_image_url}/>
                            </div> : null}
                    </div>
                    {this.state.num_of_listings > 0 ? <div>
                            <div className="row">
                                <div className="col-sm-12">
                                    {this.state.num_of_pages > 1 ? <div style={{float: 'right'}}>
                                            <PaginationControls decCurrentPage={this.decCurrentPage.bind(this)}
                                                                incCurrentPage={this.incCurrentPage.bind(this)}
                                                                page_options={page_options} page={this.state.page}
                                                                doUpdatePage={this.doUpdatePage.bind(this)}/>
                                        </div> : null}
                                    {this.state.list_type !== 'search' ? <div style={{float: 'right', marginRight: 20}}>
                                            <select onChange={this.doUpdateSortOrder.bind(this)}
                                                    value={this.state.sort_by} style={{width: '100%'}}>
                                                <option value="newest">View By Newest</option>
                                                <option value="price_asc">View From Lowest Price</option>
                                                <option value="price_desc">View From Highest Price</option>
                                            </select>
                                        </div> : null}
                                </div>
                            </div>
                            <div className="row">
                                {products}
                            </div>
                            <div style={{marginTop: 30}} className="row">
                                <div className="col-sm-12">
                                    {this.state.num_of_pages > 1 ? <div style={{float: 'right'}}>
                                            <PaginationControls decCurrentPage={this.decCurrentPage.bind(this)}
                                                                incCurrentPage={this.incCurrentPage.bind(this)}
                                                                page_options={page_options} page={this.state.page}
                                                                doUpdatePage={this.doUpdatePage.bind(this)}/>
                                        </div> : null}
                                </div>
                            </div>
                        </div> : <h3 style={{marginTop: 25}}>No results found...</h3>}
                </div>
            </div>
        );
    }

    static initialize()
    {
        let element = document.getElementById('product_listing_content');
        if(element)
        {
            ReactDOM.render(<ProductListingPageContent/>, element);
        }
    }
}
