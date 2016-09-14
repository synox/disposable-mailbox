import phonetic from "phonetic";

function getRandomInt(min, max) {
    return Math.floor(Math.random() * (max - min)) + min;
}


export function generateRandomUsername() {
    let username = phonetic.generate({syllables: 3, phoneticSimplicity: 1});
    if (Math.random() >= 0.5) {
        username += getRandomInt(30, 99);
    }
    return username.toLowerCase();
}

export function cleanUsername(username) {
    return username.replace(/[@].*$/, '');
}