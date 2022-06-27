# lantern_adventure_story_generator
lantern_story_generator Lantern adventure creator . Story Generator Script






# features
- Each line is a room
- Each room can have a villain blocking the exit. That villain is killed with the :item needed"
- Each villain drop a reward item. (there is a check between item needed + reward item to not create it twice)
- Exits you put here comma seperated the connections to other rooms : eg n=Lobby,up=Floor 1,w=Office room (the text is matched with the room name so it must be exactly the same)



- generator.php : this takes a CSV file seperated with | instead of comma and creates a xml for lantern.


# To Do
- Allow a room NOT to have villain (and NOT need a needed item)
- 