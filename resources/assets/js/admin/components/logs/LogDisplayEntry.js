/**
 * LogsDisplayEntry
 *
 * @author Christopher Lee Reeves <ChrisReeves12@yahoo.com>
 **/

import React from 'react';
import Dispatcher from 'common/Dispatcher';

export default class LogDisplayEntry extends React.Component
{
    toggleExpand(e)
    {
        e.preventDefault();

        Dispatcher.dispatch({
            type: 'LOGS_TOGGLE_EXPAND_STACK_TRACE',
            data: {idx: this.props.idx, type: this.props.type}
        });
    }

    render()
    {
        console.log(this.props);
        return(
            <div className="log-entry">
                <div className="time">{this.props.formatted_date}</div>
                <div className={"type " + (this.props.type === 'ERROR' ? 'error' : 'info')}>{this.props.type}</div>
                <h5>{this.props.message}</h5>
                {this.props.file && <div className="file">File: {this.props.file}</div>}
                {this.props.line && <div className="line">Line: {this.props.line}</div>}
                <div className="extra_data">{this.props.extra_data}</div>
                {this.props.stack_trace && <div className={"stack_trace " + (this.props.expanded ? '' : 'closed')}>
                    <a onClick={this.toggleExpand.bind(this)} href="" className="expander"><i className={"fa fa-caret-" + (this.props.expanded ? 'up' : 'down')}/></a>
                    <div dangerouslySetInnerHTML={{__html: this.props.stack_trace.replace(new RegExp("\n", 'g'), "<br/>")}}/>
                </div>}
            </div>
        );
    }
}