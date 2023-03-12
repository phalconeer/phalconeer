<?php
namespace Phalconeer\Exception\Helper;

class ReadableIdHelper
{
    /**
     * List of adjectives to be used for random generation.
     * @var array
     */
    private static $adjectives = array('Able', 'Absolute', 'Adorable', 'Academic', 'Acidic', 'Acrobatic', 'Active', 'Actual', 'Adept', 'Admired', 'Adorable', 'Adored', 'Advanced', 'Afraid', 'Aged', 'Aggressive', 'Agile', 'Agitated', 'Alarmed', 'Alive', 'Amazing', 'Ample', 'Amusing', 'Ancient', 'Angelic', 'Angry', 'Animated', 'Antique', 'Anxious', 'Apt', 'Arctic', 'Astonishing', 'Athletic', 'Attractive', 'Authentic', 'Automatic', 'Average', 'Awesome', 'Awkward', 'Bad', 'Back', 'Bare', 'Basic', 'Beautiful', 'Beloved', 'Better', 'Best', 'Big', 'Bitter', 'Blank', 'Bleak', 'Blind', 'Blissful', 'Bold', 'Boring', 'Bossy', 'Bouncy', 'Brave', 'Brief', 'Bright', 'Brilliant', 'Bruised', 'Bubbly', 'Bulky', 'Bumpy', 'Buoyant', 'Busy', 'Buzzing', 'Calm', 'Canine', 'Careful', 'Careless', 'Caring', 'Cautious', 'Charming', 'Cheap', 'Cheery', 'Chief', 'Chilly', 'Chubby', 'Classic', 'Clean', 'Clear', 'Clever', 'Clueless', 'Clumsy', 'Cold', 'Colorful', 'Colossal', 'Common', 'Competent', 'Confused', 'Conscious', 'Cool', 'Corrupt', 'Crazy', 'Creative', 'Creepy',  'Cruel', 'Cuddly', 'Curly', 'Cute', 'Dangerous', 'Decent', 'Delicious', 'Determined', 'Devoted', 'Difficult', 'Dim', 'Discrete', 'Distant', 'Dirty', 'Disguised', 'Dizzy', 'Dramatic', 'Dry', 'Eager', 'Earnest', 'Early', 'Elastic', 'Elderly', 'Elegant', 'Enchanted', 'Energetic', 'Enormous', 'Enraged', 'Evil', 'Exhausted', 'Exciting', 'Exotic', 'Expensive', 'Expert', 'Fabulous', 'Fair', 'Fake', 'Famous', 'Fancy', 'Fantastic', 'Fast', 'Fat', 'Favorite', 'Filthy', 'Fine', 'Firm', 'Flat', 'Fluffy', 'Formal', 'Free', 'Fresh', 'Friendly', 'Frosty', 'Frozen', 'Gargantuan', 'Gentle', 'Giant', 'Gigantic', 'Glamorous', 'Glorious', 'Golden', 'Good', 'Gorgeous', 'Grand', 'Great', 'Hairy', 'Happy', 'Hard', 'Hasty', 'Healthy', 'Heavy', 'High', 'Huge', 'Humble', 'Humming', 'Icy', 'Idle', 'Ill', 'Imperfect', 'Important', 'Impressive', 'Innocent','Intelligent', 'Joyful', 'Junior', 'Kind', 'Last', 'Late', 'Lazy', 'Lean', 'Light', 'Likable', 'Lively', 'Livid', 'Lonely', 'Long', 'Loud', 'Lovely', 'Loyal', 'Lucky', 'Mad', 'Magnificent', 'Massive', 'Mature', 'Mean','Medium', 'Merry', 'Messy', 'Misty', 'Monumental', 'Naive', 'Nasty', 'Natural', 'New', 'Nice', 'Noisy', 'Normal','Novel', 'Numb', 'Oily', 'Odd', 'Old', 'Optimistic', 'Ordinary', 'Original', 'Pale', 'Peaceful', 'Perfect', 'Plain', 'Playful', 'Poor', 'Popular', 'Posh', 'Powerful', 'Pretty', 'Precious', 'Proud', 'Pure', 'Quick', 'Quiet', 'Rare', 'Real', 'Remote', 'Ringed', 'Royal', 'Rude', 'Sad', 'Shy', 'Silent', 'Silky', 'Simple', 'Skinny', 'Slim', 'Slow', 'Small', 'Smart', 'Soft', 'Spanish', 'Spotted', 'Stiff', 'Striped', 'Strong', 'Stunning', 'Super', 'Sweet', 'Swift', 'Tall', 'Tame', 'Terrific', 'Thick', 'Thin', 'Thirsty', 'Tiny', 'Tough', 'Trained', 'Trusty', 'Ugly', 'Urban', 'Vivid', 'Warm', 'Wavy', 'Wealthy', 'Weak', 'Weird', 'Wet', 'Wiggly', 'Wild', 'Wise', 'Wonderful', 'Young');
    
    /**
     * List of animals to be used for random generation.
     * @var array
     */
    private static $animals = array('Aardwolf', 'Albatross', 'Alligator', 'Alpaca', 'Anaconda', 'Ant', 'Antelope', 'Armadillo', 'Baboon', 'Badger', 'Bandicoot', 'Bat', 'Bear', 'Beaver', 'Bird', 'Bison', 'Boa', 'Boar', 'Bobcat', 'Buffalo', 'Butterfly', 'Caiman', 'Camel', 'Capybara', 'Caracal', 'Cardinal', 'Caribou', 'Cat', 'Catfish', 'Chameleon', 'Cheetah', 'Chickadee', 'Chimpanzee', 'Chipmunk', 'Cobra', 'Cormorant', 'Cottonmouth', 'Cougar', 'Cow', 'Coyote', 'Crab', 'Crane', 'Crocodile', 'Crow', 'Deer', 'Dingo', 'Dog', 'Dolphin', 'Dove', 'Dragon', 'Duck', 'Eagle', 'Elephant', 'Elk', 'Emu', 'Falcon', 'Ferret', 'Flamingo', 'Fox', 'Frog', 'Gazelle', 'Gecko', 'Giraffe', 'Gnu', 'Goat', 'Goose', 'Gorilla', 'Grizzly', 'Groundhog', 'Hare', 'Hawk', 'Hedgehog', 'Hen', 'Hippopotamus', 'Hummingbird', 'Hyena', 'Ibis', 'Iguana', 'Impala', 'Jacana', 'Jackal', 'Jaguar', 'Kangaroo', 'Kite', 'Koala', 'Kudu', 'Lemming', 'Lemur', 'Leopard', 'Lion', 'Lizard', 'Llama', 'Loris', 'Lynx', 'Macaque', 'Macaw', 'Magpie', 'Meerkat', 'Mockingbird', 'Mongoose', 'Monkey', 'Moose', 'Mouflon', 'Ocelot', 'Opossum', 'Orca', 'Oryx', 'Ostrich', 'Otter', 'Owl', 'Ox', 'Parrot', 'Peacock', 'Pelican', 'Penguin', 'Pheasant', 'Pigeon', 'Platypus', 'Porcupine', 'Puffin', 'Puma', 'Python', 'Quail', 'Rabbit', 'Raccoon', 'Rat', 'Rattlesnake', 'Raven', 'Rhinoceros', 'Roadrunner', 'Robin', 'Salmon', 'Seal', 'Shark', 'Sheep', 'Skunk', 'Sloth', 'Snake', 'Sparrow', 'Spider', 'Squirrel', 'Starfish', 'Stork', 'Suricate', 'Swan', 'Tapir', 'Tarantula', 'Tiger', 'Tortoise', 'Toucan', 'Turkey', 'Turtle', 'Viper', 'Vulture', 'Wallaby', 'Warthog', 'Whale', 'Wolf', 'Wombat', 'Woodpecker', 'Yak', 'Zebra');

    /**
     * List of colors to be used for random generation.
     * @var array
     */
    private static $colors = array('Black', 'Night', 'Gunmetal', 'Midnight', 'Charcoal', 'Oil', 'Iridium', 'Gray', 'Granite', 'Platinum', 'Blue', 'Azure', 'Aquamarine', 'Turquoise', 'Teal', 'Green', 'Emerald', 'Yellow', 'Gold', 'Beige', 'Blonde', 'Champagne', 'Vanilla', 'Peach', 'Mustard', 'Sand', 'Brass', 'Khaki', 'Bronze', 'Cinnamon', 'Copper', 'Wood', 'Moccasin', 'Sepia', 'Rust', 'Chocolate', 'Orange', 'Coral', 'Red', 'Scarlet', 'Ruby', 'Mahogany', 'Cranberry', 'Burgundy', 'Sienna', 'Firebrick', 'Maroon', 'Velvet', 'Rose', 'Pink', 'Magenta', 'Rogue', 'Plum', 'Indigo', 'Grape', 'Violet', 'Purple', 'Crimson', 'Lilac', 'Mauve', 'Pearl', 'White', 'Cyan', 'Lime', 'Silver', 'Brown', 'Olive');
    
    /**
     * Amount of elements in the adjective list.
     * @var integer
     */
    private static $adjectivesLength = 0;
    
    /**
     * Amount of elements in the animals list.
     * @var integer
     */
    private static $animalsLength = 0;
    
    /**
     * Amount of elements in the colors list.
     * @var integer
     */
    private static $colorsLength = 0;
    
    /**
     * Generates a random ID, using the adjective-adjective-color-animal format
     * @return string
     */
    public static function getId()
    {
        if (self::$adjectivesLength == 0) {
            self::$adjectivesLength = count(self::$adjectives);
        }
        if (self::$animalsLength == 0) {
            self::$animalsLength = count(self::$animals);
        }
        if (self::$colorsLength == 0) {
            self::$colorsLength = count(self::$colors);
        }
        $id = [
            self::$adjectives[floor(abs(mt_rand(0, self::$adjectivesLength)-.1))],
            self::$adjectives[floor(abs(mt_rand(0, self::$adjectivesLength)-.1))],
            self::$colors[floor(abs(mt_rand(0, self::$colorsLength)-.1))],
            self::$animals[floor(abs(mt_rand(0, self::$animalsLength)-.1))],
        ];
        
        return implode('', $id);
    }
}