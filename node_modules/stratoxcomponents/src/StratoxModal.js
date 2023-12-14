
export function StratoxModal(data, container, helper, builder)
{
    const inst = this, methods = {
        init: function (config) {
            methods.config = {
                id: "modal"
            };
            Object.assign(methods.config, config);
            // Private
            methods.data = {};
            // Load modal
            this.setup();
        },
        setup: function () {
            if (!inst.hasView()) {
                this.data.type = data?.type?.toString();

                if (!this.data.type) {
                    this.data.type = "message";
                }

                this.data.isOpener = (this.data.type === "opener");
                this.data.body = document.body;
                this.data.body.classList.add("overflow");

                this.data.body.insertAdjacentHTML('beforeend', this.output.main());
                this.data.modal = inst.setSelector("#"+this.config.id);
                // Pass modal container to Stratox as the main element
                inst.setElement("#modal-content");

                // Pass close modal trigger to the container
                container.set("modal-close", methods.removeModal.bind(this));

                // Events
                inst.bindEvent(this.data.modal, "click",  ".btn", this.confirm);
                if (this.data.type === "message" || this.data.type === "error") {
                    // Remove modal on modal click
                    inst.bindEvent(this.data.modal, "click", function (e) {
                        e.preventDefault();
                        methods.removeModal();
                    });
                }

                inst.bindEvent([document], "keyup", methods.keyup);
                
            } else {
                // View Template and modal container element has been initated
                // Return template output to the script, the observer will auto update the information
                return this.output.main();
            }

        },
        output: {
            main: function () {
                return `
                <div id="${methods.config.id}" class="modal modal-${methods.data.type} abs fixed z-50 scroll">
                    <div class="flex justify-center items-center h-12">
                        <div class="relative z-20 width-100 max-height-100 ${methods.data.isOpener ? 'max-w-screen-md' : 'max-w-screen-xs'}">
                            <div class="mod-holder ${methods.data.isOpener ? '' : 'align-center'} bg-white">
                                ${this.close()}
                                <div class="card-2">
                                    <section id="modal-content" class="${methods.data.isOpener ? 'mb-30' : 'message'}">
                                        ${data.headline ? '<h2 class="headline-4 mt-0">'+data.headline+'</h2>' : ''}
                                        <p>${data.content}</p>
                                    </section>
                                    ${this.aside()}
                                </div>
                            </div>
                        </div>
                        <div class="bg-black opacity-80 abs fixed z-10"></div>
                    </div>
                </div>
                `;
            },
            close: function () {
                return `
                <a class="btn close cancel abs top right z-20 p-15" href="#">
                    <svg class="close" width="12" height="12" viewBox="0 0 12 12" xmlns="http://www.w3.org/2000/svg"><path d="M10.243 1.757l-8.486 8.486m0-8.486l8.486 8.486" stroke="currentColor" stroke-width="2" fill="none" fill-rule="evenodd" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </a>
                `;
            },
            components: function () {
                let out = "";
                if(data?.config?.form) {
                    out += `<form action="${data.config.form.action ? data.config.form.action : "#"}" method="${data.config.form.method ? data.config.form.method : "post"}">`;
                }
                builder.groupFactory(function (o, val) {
                    out += o;
                });
                if(data?.config?.form) {
                    if(data?.config?.form?.button)  out += `<input class="button" type="submit" name="submit" value="${data.config.form.button}">`;
                    out += `</form>`;
                }
                return out;
            },
            aside: function () {
                let out = "";

                // SUB COMPONENTS
                out += this.components();

                // BUTTONS
                switch (data?.type) {
                    case "confirm":
                        out += `
                        <aside class="modal-buttons mt items gap-x-10">
                            <a class="button v3 btn bg-secondary cancel" href="#">Avbryt</a>
                            <a class="button v3 btn bg-primary confirm" href="#">Bekräfta</a>
                        </aside>
                        `;
                    break;
                    case "ok":
                        out += `
                        <aside class="modal-buttons mt items gap-x-10">
                            <a class="button v3 btn bg-primary confirm" href="#">Ok</a>
                        </aside>
                        `;
                    break;
                }
                return out;
            }
        },
        removeModal: function () {
            this.data.modal[0].remove();
            this.data.body.classList.remove("overflow");
            if(typeof document?.off === "function") document.off();

        },
        confirm: function (e) {
            e.preventDefault();

            // Callback
            if (this.classList.contains("confirm")) {
                if (container.has("confirm")) {
                    container.get("confirm");
                }
            }
            methods.removeModal();

        }, keyup: function (e) {
            if (e.which === 27 && data?.type === "opener") {
                e.preventDefault();
                methods.removeModal();
            }
        }
    }

    let getReturnType = methods.init({
        body: "body",
        id: "modal_"+this.getViewCount()
    });

    // Return string to Stratox
    // Element has been set, return string to Stratox
    // On update it will auto update the modal
    if(typeof getReturnType === "string") {
        return getReturnType;
    }
}

