import React from 'react';

export default class PaginationControls extends React.Component
{
  render()
  {
    return(
      <div className="pagination-controls">
        <a onClick={this.props.decCurrentPage} href=""><i className="fa fa-arrow-circle-left"/></a>
        <select value={this.props.page} onChange={this.props.doUpdatePage}>
            {this.props.page_options}
        </select>
        <a onClick={this.props.incCurrentPage} href=""><i className="fa fa-arrow-circle-right"/></a>
      </div>
    );
  }
}
