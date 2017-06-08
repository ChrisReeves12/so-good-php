/**
 * LogsDisplay
 *
 * @author Christopher Lee Reeves <ChrisReeves12@yahoo.com>
 **/

import React from 'react';
import ReactDOM from 'react-dom';
import LogsStore from 'stores/LogsStore'
import LogDisplayEntry from './LogDisplayEntry';
import Dispatcher from 'common/Dispatcher';

export default class LogsDisplay extends React.Component
{
    constructor()
    {
        super();
        Dispatcher.dispatch({
            type: 'LOG_DISPLAY_INITIAL_DATA',
            data: window.sogood.reactjs.initial_data
        });
    }

    componentWillMount()
    {
        this.setState(this.getStateFromStore());
    }

    componentDidMount()
    {
        LogsStore.on('update', () => {
            this.setState(this.getStateFromStore());
        });
    }

    getStateFromStore()
    {
        return LogsStore.getState();
    }

    renderErrorLogs()
    {
        let idx = -1;
        return this.state.errors.map(log => {
            idx++;
            log.idx = idx;
            return(<LogDisplayEntry key={idx} {...log}/>);
        });
    }

    render()
    {
        return(
            <div>
                <ul className="nav nav-pills">
                    <li className="nav-item"><a className="nav-link active" data-toggle="tab" href="#error">Error</a></li>
                    <li className="nav-item"><a className="nav-link" data-toggle="tab" href="#info">Info</a></li>
                    <li className="nav-item"><a className="nav-link" data-toggle="tab" href="#debug">Debug</a></li>
                </ul>
                <div className="tab-content">
                    <div id="error" className="tab-pane fade in active">
                        {this.renderErrorLogs()}
                    </div>
                    <div id="info" className="tab-pane fade">
                        Info List
                    </div>
                    <div id="debug" className="tab-pane fade">
                        Debug List
                    </div>
                </div>
            </div>
        );
    }

    static initialize()
    {
        let element = document.getElementById('logs');
        if(element)
        {
            ReactDOM.render(<LogsDisplay/>, element);
        }
    }
}