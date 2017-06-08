/**
 * LogsStore
 *
 * @author Christopher Lee Reeves <ChrisReeves12@yahoo.com>
 **/

import { EventEmitter } from 'events';
import Dispatcher from 'common/Dispatcher';

class LogsStore extends EventEmitter
{
    constructor()
    {
        super();
        this.state = {};
    }

    getState()
    {
        return this.state;
    }

    handleActions(action)
    {
        switch(action.type)
        {
            case 'LOG_DISPLAY_INITIAL_DATA':
            {
                this.state = action.data;
                this.emit('update');
                break;
            }

            case 'LOGS_TOGGLE_EXPAND_STACK_TRACE':
            {
                if(action.data.type === 'ERROR')
                {
                    if(typeof this.state.errors[action.data.idx].expanded === 'undefined')
                    {
                        this.state.errors[action.data.idx].expanded = true;
                    }
                    else
                    {
                        this.state.errors[action.data.idx].expanded = !this.state.errors[action.data.idx].expanded;
                    }
                }

                this.emit('update');
                break;
            }
        }
    }
}

const store = new LogsStore();
Dispatcher.register(store.handleActions.bind(store));

export default store;