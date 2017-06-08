import React from 'react';

export default class PriceFacets extends React.Component {

    handleUpdateFilter(e)
    {
        e.preventDefault();

        if(e.target.dataset.filter)
        {
            // Capture value for desktop version
            this.props.doUpdatePriceFilter(e.target.dataset.filter);
        }
        else
        {
            // Capture value for mobile version
            this.props.doUpdatePriceFilter(e.target.value);
        }
    }

    renderDesktopVersion()
    {
        return(
            <div className="hidden-md-down">
                <h4>By Price</h4>
                <ul className="price-filter">
                    <li className={(this.props.price_filter == 'all') ? 'selected' : ''}><a
                        onClick={this.handleUpdateFilter.bind(this)} data-filter='all' href=''>All Prices</a></li>
                    <li className={(this.props.price_filter == '0_25') ? 'selected' : ''}><a
                        onClick={this.handleUpdateFilter.bind(this)} data-filter='0_25' href=''>$0 - $25</a></li>
                    <li className={(this.props.price_filter == '25_50') ? 'selected' : ''}><a
                        onClick={this.handleUpdateFilter.bind(this)} data-filter='25_50' href=''>$25 - $50</a></li>
                    <li className={(this.props.price_filter == '50_75') ? 'selected' : ''}><a
                        onClick={this.handleUpdateFilter.bind(this)} data-filter='50_75' href=''>$50 - $75</a></li>
                    <li className={(this.props.price_filter == '75_100') ? 'selected' : ''}><a
                        onClick={this.handleUpdateFilter.bind(this)} data-filter='75_100' href=''>$75 - $100</a></li>
                    <li className={(this.props.price_filter == '100_*') ? 'selected' : ''}><a
                        onClick={this.handleUpdateFilter.bind(this)} data-filter='100_*' href=''>$100+</a></li>
                </ul>
            </div>
        );
    }

    renderMobileVersion()
    {
        return(
            <div className="hidden-lg-up">
                <label style={{fontWeight: 'bold'}} className="form-control-label">Filter By Price</label>
                <div className="form-group">
                    <select value={this.props.price_filter} onChange={this.handleUpdateFilter.bind(this)} className="form-control">
                        <option value="all">All Prices</option>
                        <option value="0_25">$0 - $25</option>
                        <option value="25_50">$25 - $50</option>
                        <option value="50_75">$50 - $75</option>
                        <option value="75_100">$75 - $100</option>
                        <option value="100_*">$100+</option>
                    </select>
                </div>
            </div>
        );
    }

    render() {
        return (
            <div>
                {this.renderDesktopVersion()}
                {this.renderMobileVersion()}
            </div>
        );
    }
}
