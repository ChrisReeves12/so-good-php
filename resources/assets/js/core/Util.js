export default class Util
{
    static get_auth_token()
    {
        return $("meta[name='csrf-token']").attr('content');
    }

    static get_use_cache()
    {
        return $("meta[name='use-cache']").attr('content');
    }

    static env()
    {
        return $("meta[name='env']").attr('content');
    }

    static objectIsEmpty(obj)
    {
        if(typeof obj == 'object' && obj !== null)
        {
            for(let prop in obj) {
                if(obj.hasOwnProperty(prop))
                    return false;
            }
        }
        else if(Array.isArray())
        {
            if(obj.length > 0)
                return false;
        }

        return true;
    }

    static fillArray(value) {
      let arr = [];
      for(let x = 0; x < value; x++)
      {
        arr.push(x);
      }

      return arr;
    };
}
