/**
 * HomeCourasel
 *
 * @author Christopher Lee Reeves <ChrisReeves12@yahoo.com>
 **/

import React from 'react';
import ReactDOM from 'react-dom';
import MiniBuyBox from 'components/mini_buy_box/MiniBuyBox';

export default class HomeCourasel extends React.Component
{
    constructor(props)
    {
        super(props);
        this.state = window.sogood.reactjs.courasel[props.id];
        this.id = props.id;
        this.panel_width = 100000;
        this.state.panel_left = 0;
        this.state.panel_width = 100000;
        this.state.panel_height = 0;
        this.state.show_dir_buttons = false;
        this.state.right_limit_reached = false;
        this.state.left_limit_reached = true;
        this.state.is_mobile = false;
    }

    componentDidMount()
    {
        // Get how long to make the panel
        this._resizePanel();
        this._evalScrollButtons(this.state.panel_left);

        $(window).on('resize', (e) => {
            this._resizePanel();
            this._evalScrollButtons(this.state.panel_left);
        });
    }

    scrollLeft(e)
    {
        e.preventDefault();

        if(this.state.show_dir_buttons || this.state.is_mobile)
        {
            let container_width = $('#' + this.id + '_sg_courasel').outerWidth();
            let new_left_pos = this.state.panel_left - container_width;

            if(new_left_pos < (-this.panel_width + container_width))
            {
                new_left_pos = (-this.panel_width + container_width);
            }

            this.setState({panel_left: new_left_pos});

            this._evalScrollButtons(new_left_pos);
        }
    }

    scrollRight(e)
    {
        e.preventDefault();

        if(this.state.show_dir_buttons || this.state.is_mobile)
        {
            let move_width = $('#' + this.id + '_sg_courasel').outerWidth();
            let new_left_pos = this.state.panel_left + move_width;
            if(new_left_pos > 0)
            {
                new_left_pos = 0;
            }

            this.setState({panel_left: new_left_pos});

            this._evalScrollButtons(new_left_pos);
        }
    }

    _evalScrollButtons(left_pos)
    {
        let container_width = $('#' + this.id + '_sg_courasel').outerWidth();
        let left_limit_reached = left_pos >= 0;
        let right_limit_reached = left_pos <= (-this.panel_width + container_width);

        this.setState({right_limit_reached, left_limit_reached});
    }

    _resizePanel()
    {
        let element_id = this.id + '_sg_courasel';
        this.panel_width = 0;
        let is_mobile = $(window).width() < 992;
        let panel_height = 0;
        let panel_slides = $('#' + element_id).find('.slide');
        for(let x = 0; x < panel_slides.length; x++)
        {
            let slide = panel_slides[x];
            let slide_element = $(slide);
            this.panel_width += slide_element.outerWidth();

            if(slide_element.outerHeight() > panel_height)
                panel_height = slide_element.outerHeight();
        }

        this.setState({panel_width: this.panel_width, panel_height: panel_height, is_mobile});
    }

    showDirectionButtons(e)
    {
        if(!this.state.is_mobile)
            this.setState({show_dir_buttons: true});
    }

    hideDirectionButtons(e)
    {
        if(!this.state.is_mobile)
            this.setState({show_dir_buttons: false});
    }

    render()
    {
        let button_final_opacity = 0.7;

        return(
            <div onMouseLeave={this.hideDirectionButtons.bind(this)} onMouseEnter={this.showDirectionButtons.bind(this)}>
                <a className="dir-button left" style={{
                    cursor: (((this.state.show_dir_buttons || this.state.is_mobile) && !this.state.left_limit_reached) ? 'pointer' : 'inherit'),
                    opacity: (((this.state.show_dir_buttons || this.state.is_mobile) && !this.state.left_limit_reached) ? button_final_opacity : (this.state.left_limit_reached ? 0 : 0.4))
                }} href=""
                   onClick={this.scrollRight.bind(this)}><i className="fa fa-chevron-left"/></a>

                <a className="dir-button right" style={{
                    cursor: (((this.state.show_dir_buttons || this.state.is_mobile) && !this.state.right_limit_reached) ? 'pointer' : 'inherit'),
                    opacity: (((this.state.show_dir_buttons || this.state.is_mobile) && !this.state.right_limit_reached) ? button_final_opacity : (this.state.right_limit_reached ? 0 : 0.4))
                }} href=""
                   onClick={this.scrollLeft.bind(this)}><i className="fa fa-chevron-right"/></a>

                <div style={{height: this.state.panel_height}} className="courasel-container" id={this.id + '_sg_courasel'}>
                    <div style={{width: this.state.panel_width, height: this.state.panel_height, left: this.state.panel_left}} className="panel">
                        {(() => {
                            return this.state.slides.map(slide => {
                                return(
                                    <div className="slide">
                                        <MiniBuyBox hide_add_to_cart={true} idx={slide.id} data={slide}/>
                                    </div>
                                );
                            });
                        })()}
                    </div>
                </div>
            </div>
        );
    }

    static initialize()
    {
        let elements = document.getElementsByClassName('sg-courasel');
        for(let x = 0; x < elements.length; x++)
        {
            let element = elements[x];
            ReactDOM.render(<HomeCourasel id={element.dataset.id}/>, element);
        }
    }
}
