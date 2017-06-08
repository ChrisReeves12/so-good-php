import React from 'react';

export default class TabContentListRenderer
{
    static render(content)
    {
        let idx = -1;
        let lines = content.map(list_item => {
            idx++;
            return(
                <div key={idx} className={"list-item " + ((idx % 2 > 0) ? ' darker' : '')}>
                    <div className="key-name">{list_item.key}</div>
                    <div className="value">{list_item.value}</div>
                </div>
            );
        });

        return(
            <div className="specs-list">
                {lines}
            </div>
        );
    }
}