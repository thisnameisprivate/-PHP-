const mySetInterval = (cb, time) => {
    const fn = () => {
        cb();
        setTimeout(() => {
            fn()
        }, time)
    }
    setTimeout(fn, time)
}

mySetInterval(() => {
    console.log(new Date());
}, 1000)