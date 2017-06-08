/**
 * Class definition of PaginationWidget
 * @author Christopher Reeves <chrisreeves12@yahoo.com>
 */

import React from 'react';
import ReactDOM from 'react-dom';

export default class PaginationWidget extends React.Component {
    constructor(props) {
        super(props);
        this.state = Object.assign({}, props);

        if(!Array.prototype.chunk)
        {
            Object.defineProperty(Array.prototype, 'chunk', {
                value: function(chunkSize) {
                    let R = [];
                    for (let i=0; i<this.length; i+=chunkSize)
                        R.push(this.slice(i,i+chunkSize));
                    return R;
                }
            });
        }
    }

    render() {

        if(this.state.page_count < 2)
            return null;

        let pages = Util.fillArray(this.state.page_count);
        let sections = pages.chunk(this.state.range_size);
        let section_count = sections.length;
        let current_section_idx = -1;

        // If the last section is short, add it to the next to last one, and remove the last section
        if(section_count > 1)
        {
            if(sections[section_count-1].length < this.state.range_size)
            {
                sections[section_count-2] = sections[section_count-2].concat(sections[section_count-1]);
                sections.pop();
                section_count = sections.length;
            }
        }

        // Find the section the current page belong to
        let section = sections.find(sec => {
            current_section_idx++;
            return(sec.filter((p) => (p === (this.state.page - 1))).length > 0);
        });

        return(
            <div>
                <ul className="pagination">
                    {(current_section_idx !== 0) && <li  className="arrow-icon"><a href={`${this.state.base_url}?page=1`}><i className="fa fa-angle-double-left"/></a></li>}
                    {(this.state.page > 1)
                        && <li className="arrow-icon"><a href={`${this.state.base_url}?page=${this.state.page-1}`}><i className="fa fa-angle-left"/></a></li>}
                    {(() => {
                        return section.map((i) => {
                            return(
                                <li className={(i == (this.state.page-1)) ? 'selected' : ''} key={i+1}>
                                    <a href={`${this.state.base_url}?page=${i+1}`}>{i+1}</a>
                                </li>
                            );
                        });
                    })()}
                    {(this.state.page !== (this.state.page_count)) &&
                        <li className="arrow-icon"><a href={`${this.state.base_url}?page=${this.state.page+1}`}><i className="fa fa-angle-right"/></a></li>}
                    {(current_section_idx !== section_count-1) && <li className="arrow-icon"><a href={`${this.state.base_url}?page=${this.state.page_count}`}><i className="fa fa-angle-double-right"/></a></li>}
                </ul>
            </div>
        );
    }

    static initialize()
    {
        let elements = document.getElementsByClassName('pagination-widget');

        if(elements.length > 0)
        {
            for(let element of elements)
            {
                if(element)
                    ReactDOM.render(<PaginationWidget base_url={element.dataset.baseUrl} range_size={parseInt(element.dataset.rangeSize)}
                                                  page={parseInt(element.dataset.currentPage)} page_count={parseInt(element.dataset.pageCount)}/>, element);
            }
        }
    }
}