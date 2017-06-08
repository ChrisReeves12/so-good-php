import React from 'react';

export default class TabContentTextRenderer
{
    static render(content)
    {
        return (<p className="tab-text-content" dangerouslySetInnerHTML={{__html: content.replace(/\n/, "<br/>")}}/>);
    }
}