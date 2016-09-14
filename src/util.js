import phonetic from "phonetic";

export function generateRandomUsername() {
    let username = phonetic.generate({syllables: 3, phoneticSimplicity: 1});
    if (Math.random() >= 0.5) {
        username += this.getRandomInt(30, 99);
    }
    return username.toLowerCase();
}

function getRandomInt(min, max) {
    return Math.floor(Math.random() * (max - min)) + min;
}

export function cleanUsername(username) {
    return username.replace(/[@].*$/, '');
}