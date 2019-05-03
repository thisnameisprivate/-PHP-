if (document.getElementById('IdenController-item')) {
    console.log("Hello, world.");
}

var item = document.getElementsByTagName('item-0')[0];
item.addEventListener('click', () => {
    console.log(":console.log('JavaScript;')");
})
item.removeEventListener('click');