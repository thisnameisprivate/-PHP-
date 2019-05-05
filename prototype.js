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


//
function GrandFather () {
    this.name = 'GrandFather';
}
function Father () {
    this.age = 22;
}
Father.prototype = new GrandFather();
function Son () {}
console.log(son.name);
console.log(son.age);
console.log(Son.prototype.constructor);