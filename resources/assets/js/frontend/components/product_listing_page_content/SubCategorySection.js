import React from 'react';

export default class SubCategorySection extends React.Component
{
  goToCategory(e)
  {
    if(e.target.value)
    {
      let value = e.target.value;
      window.location = `/category/${value}`;
    }
  }

  renderMobileVersion()
  {
    return(
        <div className="form-group hidden-lg-up">
          <select onChange={this.goToCategory.bind(this)} className="form-control">
            <option value=''>Select Sub-Category</option>
              {(() => {
                  return this.props.sub_categories.map(sb => {
                      return(
                          <option key={sb.slug} value={sb.slug}>{sb.name}</option>
                      );
                  });
              })()}
          </select>
        </div>
    );
  }

  renderDesktopVersion()
  {
    return(<ul className="hidden-md-down">
        {(() => {
            let idx = -1;
            return this.props.sub_categories.map(sb => {
                idx++;
                return(
                    <li key={idx}><a href={`/category/${sb.slug}`}>{sb.name}</a></li>
                );
            });
        })()}
    </ul>);
  }

  render()
  {
    let desktop_sub_categories = null;
    let mobile_sub_categories = null;

    if(this.props.sub_categories)
    {
      if(this.props.sub_categories.length > 0)
      {
          desktop_sub_categories = this.renderDesktopVersion();
          mobile_sub_categories = this.renderMobileVersion();
      }
    }

    let title_section = null;
    if(this.props.list_type == 'category')
    {
      title_section = (
        <div className="facet-section-block">
          <h4>{this.props.title}</h4>
          {desktop_sub_categories}
          {mobile_sub_categories}
        </div>
        );
    }
    else if(this.props.list_type == 'search')
    {
      title_section = (
        <div className="facet-section-block">
          <h4>Search Results</h4>
          <p>{this.props.num_of_listings} results for "{this.props.keyword}"</p>
        </div>
      );
    }

    return title_section;
  }
}
