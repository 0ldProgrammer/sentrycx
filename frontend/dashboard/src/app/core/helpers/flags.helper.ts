import { Injectable } from '@angular/core';
import * as _ from 'lodash';
import { groupBy, sum } from 'lodash';

@Injectable()
export class FlagsHelper{


  static countFlagsColor( affectedSummary ){
    const colors = [0,0,0]; // 0 => AMBER, 1 => YELLOW, 2 => RED

    const category = ['application', 'voice', 'network'];

    affectedSummary.forEach( item => {
      category.forEach( (cat) => {
        const categoryData = item[cat];
        if( categoryData.percentage > 10 ){
          colors[2] += 1;
          return;
        }

        if( 5 < categoryData.percentage && categoryData.percentage < 10 ){
          colors[1] += 1;
          return;
        }

        if( 1 < categoryData.percentage && categoryData.percentage <= 5 ){
          colors[0] += 1;
          return;
        }
      });
    });

    return colors;

  }

  static countFlagsByType( categoryInfo, color ){
    return categoryInfo['application'][color]
      + categoryInfo['voice'][color]
      + categoryInfo['network'][color];
  }

  static groupByAccount( flagOverview : Array<any> ){
    let self = this;
    let byAccount = _.groupBy( flagOverview, 'account');

    let summaryList = _.map( byAccount, ( summary, accountName ) => {
      let alertByGroup = {
        voice : self._getCount( summary, 'voice', 'alert_count'),
        application : self._getCount( summary, 'application', 'alert_count'),
        network : self._getCount( summary, 'network', 'alert_count')
      };

      let percentageByGroup = {
        voice : self._getCount( summary, 'voice', 'percentage'),
        application : self._getCount( summary, 'application', 'percentage'),
        network : self._getCount( summary, 'network', 'percentage')
      }

      let data = {
        account : accountName,
        alerts : alertByGroup.voice + alertByGroup.application + alertByGroup.network ,
        url : summary[0]['url'],
        logins : summary[0]['login_count'],
        voice : {
          percentage : percentageByGroup.voice,
          alert : alertByGroup.voice,
        },
        network :  {
          percentage : percentageByGroup.network,
          alert : alertByGroup.network,
        },
        application : {
          percentage : percentageByGroup.application,
          alert : alertByGroup.application,
        }
      }

      return data;
    });

    return summaryList;
  }
  

  static groupByCountry( flagOverview : Array<any> ){
    let self = this;

    let byCountry = _.groupBy( flagOverview, 'country');

    let summaryList = _.map( byCountry, ( summary, countryName ) => {
      console.log('summary', summary);
      let alertByGroup = {
        voice : self._getCount( summary, 'voice', 'alert_count'),
        application : self._getCount( summary, 'application', 'alert_count'),
        network : self._getCount( summary, 'network', 'alert_count')
      };

      let percentageByGroup = {
        voice : self._getCount( summary, 'voice', 'percentage'),
        application : self._getCount( summary, 'application', 'percentage'),
        network : self._getCount( summary, 'network', 'percentage')
      }

      let data = {
        // account : summary['']
        country : countryName,
        alerts : alertByGroup.voice + alertByGroup.application + alertByGroup.network ,
        logins : summary[0]['login_count'],
        voice : {
          percentage : percentageByGroup.voice,
          alert : alertByGroup.voice,
        },
        network :  {
          percentage : percentageByGroup.network,
          alert : alertByGroup.network,
        },
        application : {
          percentage : percentageByGroup.application,
          alert : alertByGroup.application,
        }
      }

      return data;
    });

    return summaryList;
  }

  static groupByLocation( flagOverview : Array<any> ){
    let self = this;

    let byLocation = _.groupBy( flagOverview, 'location');

    let summaryList = _.map( byLocation, ( summary, locationName ) => {
      let alertByGroup = {
        voice : self._getCount( summary, 'voice', 'alert_count'),
        application : self._getCount( summary, 'application', 'alert_count'),
        network : self._getCount( summary, 'network', 'alert_count')
      };

      let percentageByGroup = {
        voice : self._getCount( summary, 'voice', 'percentage'),
        application : self._getCount( summary, 'application', 'percentage'),
        network : self._getCount( summary, 'network', 'percentage')
      }

      let data = {
        // account : summary['']
        location : locationName,
        alerts : alertByGroup.voice + alertByGroup.application + alertByGroup.network ,
        logins : summary[0]['login_count'],
        voice : {
          percentage : percentageByGroup.voice,
          alert : alertByGroup.voice,
        },
        network :  {
          percentage : percentageByGroup.network,
          alert : alertByGroup.network,
        },
        application : {
          percentage : percentageByGroup.application,
          alert : alertByGroup.application,
        }
      }

      return data;
    });

    return summaryList;
  }

   static _getCount( summary, categoryName, property ){
    let data = _.find( summary, { name : categoryName} );
    if( !data )
      return 0;
    return data[property ]
  }

  static _getLoginCount( summary, categoryName ){

  }


  static filterByTag( summaryList, tagName){
    let filterTagDetails = this.getFilterTagDetails( tagName )
    return _.filter( summaryList, function( account ){
      if (
        !filterTagDetails.thresholdEnd &&
        filterTagDetails.thresholdStart < account[filterTagDetails.categoryName].percentage
      )
        return true;

      if (
        filterTagDetails.thresholdEnd > account[filterTagDetails.categoryName].percentage &&
        filterTagDetails.thresholdStart < account[filterTagDetails.categoryName].percentage
      )
        return true;

      return false;
    });
  }

  static getFilterTagDetails( tagName ){

    let tagSplit = _.split( tagName, ':', 2 );
    let categoryName = tagSplit[0];
    let flagColor    = tagSplit[1];

    const threshold = {
      RED    : { start : 10 , end : false },
      AMBER  : { start : 5 , end : 10 },
      YELLOW : { start : 0 , end : 5 },


    }

    return {
      categoryName : _.lowerCase(categoryName),
      thresholdStart : threshold[flagColor].start,
      thresholdEnd   : threshold[flagColor].end,
    }
  }

  /*
   * Generate the number of flags by Category and per color
   *
   */
  static groupByCategory( flagOverview : Array<any> ){
    let groupByCategory = _.groupBy( flagOverview, 'name' );

    let groupings = {
      application : { RED : 0, YELLOW : 0, AMBER : 0 },
      network : { RED : 0, YELLOW : 0, AMBER : 0  },
      voice : { RED : 0, YELLOW : 0 , AMBER : 0}
    };


    _.forEach( groupByCategory, (category, categoryName)=> {
      const groupByColor = _.countBy( category , (item) => {
        let flagColor = 'GREEN';

        if( item['percentage'] > 10 )
          flagColor = 'RED';
        else if ( item['percentage'] > 5)
          flagColor = 'AMBER';
        else if( item['percentage']  > 0 )
          flagColor = 'YELLOW';

        return flagColor;
      });

      groupings[ categoryName ] = _.merge({
        RED    : 0,
        AMBER  : 0,
        YELLOW : 0,
        GREEN  : 0
      }, groupByColor);
    });

    return groupings;

  }
}
