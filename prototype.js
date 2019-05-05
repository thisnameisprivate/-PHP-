function Person () {}
var proson1 = new Person();
var proson2 = new Person();

proson1.sayHello = function () {
    console.log('Hello!');
}
proson2.sayHello = function () {
    console.log('Hello!');
}
console.log(proson1.sayHello == proson2.sayHello)