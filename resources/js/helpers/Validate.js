
export class Validate {

    #value = "";
    #length = 0;
    #number = 0;
    #regex = {
        lowercase: /[a-z]/,
        uppercase: /[A-Z]/,
        number: /\d/,
        special: /[$@$!%*?&]/,
        unsupportedChar: /[^a-zA-Z\d$@$!%*?&]/g
    };

    constructor(value)
    {
        let val = (((typeof value) === "string") ? value : "");
        this.#value = val;
        this.#length = val.length;
        this.#value = val.replace(/ /g,'').replace(/-/g,'').replace(/\+/g,'');
        return this;
    }

    static value(value)
    {
        return new Validate(value);
    }

    getLength()
    {
        return this.#length;
    }

    email()
    {
        let regex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;
        return regex.test(this.#value);
    }

    phone()
    {
        let regex = /^((\+[1-9]{1,4}[ \-]*)|(\([0-9]{2,3}\)[ \-]*)|([0-9]{2,4})[ \-]*)*?[0-9]{3,4}?[ \-]*[0-9]{3,4}?$/;
        return regex.test(this.#value);
    }

    tel()
    {
        return this.phone();
    }

    number()
    {
        return $.isNumeric(this.#value);
    }

    matchRegex(key)
    {
        if (this.#regex[key] === undefined) {
            console.error("You are trying to match a regex that do not exits.");
        }
        return this.#regex[key].test(this.#value);
    }

    hasLower()
    {
        return this.matchRegex("lowercase");
    }

    hasUpper()
    {
        return this.matchRegex("uppercase");

    }

    hasNumber()
    {
        return this.matchRegex("number");
    }

    hasSpecialChar()
    {
        return this.matchRegex("special");
    }

    hasUnsupportedSpecialChar()
    {
        return this.matchRegex("unsupportedChar");
    }

    strictPassword(length)
    {
        if (typeof length !== "number") {
            length = 1;
        }
        if (!this.minLength(length)) {
            return false;
        }
        let regex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[$@$!%*?&])[A-Za-z\d$@$!%*?&]{1,}$/
        return regex.test(this.#value);
    }

    lossyPassword(length)
    {
        if (typeof length !== "number") {
            length = 1;
        }
        if (!this.minLength(length)) {
            return false;
        }
        return !this.hasUnsupportedSpecialChar();
    }

    minLength(length)
    {
        return (length <= this.#length) ? true : false;
    }

    maxLength(length)
    {
        return (length >= this.#length) ? true : false;
    }

    charLength(min, max)
    {
        min = parseInt(min);
        max = parseInt(max);

        if ((typeof min) !== "number") {
            throw new Error('Validate.charLength: Arg 1 is required and is expecting a number.');
            return false;
        }

        if (!this.minLength(min)) {
            return false;
        }
        if (((typeof max) === "number") && !this.maxLength(min)) {
            return false;
        }
        return true;
    }

}
